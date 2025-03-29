import logging
import difflib
import psycopg2
import re

from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
from rasa_sdk.events import FollowupAction, SlotSet

# ======================= LOGGER SETUP =======================
logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)

file_handler = logging.FileHandler("bot_debug.log")
file_handler.setLevel(logging.DEBUG)
formatter = logging.Formatter('%(asctime)s - %(levelname)s - %(message)s')
file_handler.setFormatter(formatter)
if not logger.handlers:
    logger.addHandler(file_handler)

console_handler = logging.StreamHandler()
console_handler.setLevel(logging.DEBUG)
console_handler.setFormatter(formatter)
logger.addHandler(console_handler)


# ---------- Azioni per Marche e Modelli ---------- #

class ActionQueryBrands(Action):
    def name(self) -> str:
        return "action_query_brands"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryBrands...")
        try:
            with psycopg2.connect(
                host="pgsql", database="app", user="app", password="password", port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    query = """
                        SELECT DISTINCT(V.NAME)
                        FROM TERMINALS T
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND V.PUBLISHED = TRUE
                        ORDER BY V.NAME;
                    """
                    logger.debug(f"Esecuzione query per le marche: {query}")
                    cursor.execute(query)
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query marche: {results}")
                    if results:
                        brand_list = [r[0] for r in results]
                        formatted = "\n".join(f"- {b}" for b in brand_list)
                        msg = f"Seleziona una marca per visualizzare i modelli:\n{formatted}"
                        dispatcher.utter_message(text=msg)
                    else:
                        dispatcher.utter_message(text="Non ci sono marche disponibili al momento.")
        except psycopg2.Error as e:
            logger.error(f"Errore in ActionQueryBrands: {e}")
            dispatcher.utter_message(text=f"Errore nel recupero delle marche. DB error: {e}")
        return []

class ActionQueryModels(Action):
    def name(self) -> str:
        return "action_query_models"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryModels...")
        brand_raw = tracker.get_slot("brand_raw")
        logger.debug(f"Slot brand_raw: {brand_raw}")
        brand_list = []
        try:
            with psycopg2.connect(host="pgsql", database="app", user="app", password="password", port=5432) as conn:
                with conn.cursor() as cursor:
                    query = """
                        SELECT DISTINCT(V.NAME)
                        FROM TERMINALS T
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND V.PUBLISHED = TRUE
                        ORDER BY V.NAME;
                    """
                    logger.debug("Esecuzione query per le marche")
                    cursor.execute(query)
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query marche: {results}")
                    brand_list = [r[0] for r in results]
        except psycopg2.Error as e:
            logger.error(f"Errore in ActionQueryModels (marche): {e}")
            dispatcher.utter_message(text=f"Errore nel recupero dei brand. DB error: {e}")
            return []

        if not brand_raw or brand_raw.upper() not in [b.upper() for b in brand_list]:
            best_match = difflib.get_close_matches(brand_raw if brand_raw else "", brand_list, n=1, cutoff=0.5)
            if best_match:
                brand_raw = best_match[0]
                dispatcher.utter_message(text=f"Forse cercavi la marca '{brand_raw}'...")
            else:
                dispatcher.utter_message(text=f"Non ho trovato nessuna marca simile a '{brand_raw}'.")
                return []

        try:
            with psycopg2.connect(host="pgsql", database="app", user="app", password="password", port=5432) as conn:
                with conn.cursor() as cursor:
                    query = """
                        SELECT T.NAME
                        FROM TERMINALS T
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        JOIN TACS TA ON TA.TERMINAL_ID = T.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND V.PUBLISHED = TRUE
                          AND V.NAME ILIKE %s
                        GROUP BY T.NAME
                        ORDER BY COUNT(TA.VALUE) DESC
                        LIMIT 15;
                    """
                    logger.debug(f"Esecuzione query modelli per marca: {brand_raw.upper()}")
                    cursor.execute(query, (brand_raw.upper(),))
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query modelli: {results}")
                    if results:
                        model_list = [r[0] for r in results]
                        formatted_models = "\n".join(f"- {m}" for m in model_list)
                        msg_models = f"Modelli più venduti per {brand_raw.upper()}:\n{formatted_models}"
                        dispatcher.utter_message(text=msg_models)
                    else:
                        dispatcher.utter_message(text=f"Non ci sono modelli trovati per la marca {brand_raw}.")
        except psycopg2.Error as e:
            logger.error(f"Errore in ActionQueryModels (modelli): {e}")
            dispatcher.utter_message(text=f"Errore nel recupero dei modelli. DB error: {e}")
        return [FollowupAction("action_reset_slots"), FollowupAction("action_return_menu")]

# ---------- Azioni per Categorie e Attributi della Categoria ---------- #

class ActionQueryCategories(Action):
    def name(self) -> str:
        return "action_query_categories"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryCategories...")
        try:
            with psycopg2.connect(host="pgsql", database="app", user="app", password="password", port=5432) as conn:
                with conn.cursor() as cursor:
                    query = """
                        SELECT DISTINCT(AC.NAME)
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                        ORDER BY AC.NAME;
                    """
                    logger.debug("Esecuzione query per le categorie")
                    cursor.execute(query)
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query categorie: {results}")
                    if results:
                        categories = [r[0] for r in results]
                        formatted = "\n".join(f"- {c}" for c in categories)
                        msg = f"Ecco le categorie disponibili:\n{formatted}"
                        dispatcher.utter_message(text=msg)
                    else:
                        dispatcher.utter_message(text="Non sono state trovate categorie.")
        except psycopg2.Error as e:
            logger.error(f"Errore in ActionQueryCategories: {e}")
            dispatcher.utter_message(text=f"Errore nel recupero delle categorie. DB error: {e}")
        return []

class ActionQueryAttributesByCategory(Action):
    def name(self) -> str:
        return "action_query_attributes_by_category"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryAttributesByCategory...")
        category_raw = tracker.get_slot("category_raw")
        logger.debug(f"Categoria ricevuta: {category_raw}")
        if not category_raw:
            dispatcher.utter_message(text="Per favore, specifica la categoria.")
            return []
        try:
            with psycopg2.connect(host="pgsql", database="app", user="app", password="password", port=5432) as conn:
                with conn.cursor() as cursor:
                    query = """
                        SELECT DISTINCT A.DESCRIPTION, AV.VALUE
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND AC.NAME ILIKE %s
                        ORDER BY A.DESCRIPTION;
                    """
                    logger.debug(f"Esecuzione query attributi per la categoria: {category_raw}")
                    cursor.execute(query, (category_raw,))
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query attributi: {results}")
                    if not results:
                        dispatcher.utter_message(text=f"Nessun attributo trovato per la categoria '{category_raw}'.")
                        return []
                    else:
                        attributes_list = [f"- {desc}: {val}" for desc, val in results]
                        formatted_attributes = "\n".join(attributes_list)
                        msg = f"Ecco gli attributi per la categoria '{category_raw}':\n{formatted_attributes}"
                        dispatcher.utter_message(text=msg)
        except psycopg2.Error as e:
            logger.error(f"Errore in ActionQueryAttributesByCategory: {e}")
            dispatcher.utter_message(text=f"Errore nel recupero degli attributi. DB error: {e}")
            return []
        # Se vengono trovati attributi, mostriamo i 2 pulsanti
        dispatcher.utter_message(
            text="Vuoi vedere gli attributi per categoria per modello oppure cercare i modelli per attributo=valore?",
            buttons=[
                {"title": "Attributi per categoria per modello", "payload": "/model_category_search"},
                {"title": "Cercare modelli per attributo", "payload": "/attribute_value_search"}
            ]
        )
        return []

# ---------- NUOVI FLUSSI BASATI SUI FORMATI ---------- #

class ActionQueryAttributesByModelAndCategory(Action):
    def name(self) -> str:
        return "action_query_attributes_by_model_and_category"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryAttributesByModelAndCategory...")
        user_input = tracker.latest_message.get("text", "").strip().strip("\"'")
        logger.debug(f"Input grezzo: {user_input}")
        if ":" in user_input:
            parts = user_input.split(":")
            model_raw = parts[0].strip()
            category_raw = parts[1].strip()
            logger.debug(f"Estrazione riuscita - model: {model_raw}, category: {category_raw}")
        else:
            model_raw, category_raw = None, None
            logger.debug("Formato non valido: ':' mancante")

        if not model_raw or not category_raw:
            logger.info("Formato non valido per 'modello:categoria'")
            dispatcher.utter_message(
                text="Mi dispiace, non ho capito il formato. Per favore, inserisci 'modello:categoria'.",
                buttons=[
                    {"title": "Attributi per categoria per modello", "payload": "/model_category_search"},
                    {"title": "Cercare modelli per attributo", "payload": "/attribute_value_search"}
                ]
            )
            return []

        try:
            with psycopg2.connect(host="pgsql", database="app", user="app", password="password", port=5432) as conn:
                with conn.cursor() as cursor:
                    query = """
                        SELECT DISTINCT A.DESCRIPTION, AV.VALUE
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND AC.NAME ILIKE %s
                          AND T.NAME ILIKE %s
                        ORDER BY A.DESCRIPTION;
                    """
                    logger.debug(f"Esecuzione query con category: {category_raw} e model pattern: %{model_raw}%")
                    cursor.execute(query, (category_raw, f"%{model_raw}%"))
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query: {results}")
                    if results:
                        attributes_list = [f"- {desc}: {val}" for desc, val in results]
                        formatted_attributes = "\n".join(attributes_list)
                        msg = f"Ecco gli attributi per il modello '{model_raw}' (categoria: {category_raw}):\n{formatted_attributes}"
                        dispatcher.utter_message(text=msg)
                        logger.info("Query completata con successo")
                    else:
                        logger.info("Nessun attributo trovato per il modello e categoria indicati")
                        dispatcher.utter_message(text=f"Nessun attributo trovato per il modello '{model_raw}' nella categoria '{category_raw}'.")
        except psycopg2.Error as e:
            logger.error(f"Errore in ActionQueryAttributesByModelAndCategory: {e}")
            dispatcher.utter_message(text=f"Errore nel recupero degli attributi. DB error: {e}")
        return [FollowupAction("action_reset_slots"), FollowupAction("action_return_menu")]

class ActionQueryDevicesByAttributeValue2(Action):
    def name(self) -> str:
        return "action_query_devices_by_attribute_value_2"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryDevicesByAttributeValue2...")
        user_input = tracker.latest_message.get("text", "").strip().strip("\"'")
        logger.debug(f"Input grezzo: {user_input}")
        if not user_input:
            logger.info("Input vuoto per 'attributo:valore'")
            dispatcher.utter_message(text="Per favore, inserisci il valore nel formato 'attributo:valore'.")
            return []
        if ":" in user_input:
            parts = user_input.split(":")
            attribute_desc_raw = parts[0].strip()
            attribute_val_raw = parts[1].strip()
            logger.debug(f"Estrazione riuscita - attributo: {attribute_desc_raw}, valore: {attribute_val_raw}")
        else:
            attribute_desc_raw, attribute_val_raw = None, None
            logger.debug("Formato non valido: ':' mancante")

        if not attribute_desc_raw or not attribute_val_raw:
            logger.info("Formato non valido per 'attributo:valore'")
            dispatcher.utter_message(
                text="Mi dispiace, non ho capito il formato. Per favore, inserisci 'attributo:valore'.",
                buttons=[
                    {"title": "Attributi per categoria per modello", "payload": "/model_category_search"},
                    {"title": "Cercare modelli per attributo", "payload": "/attribute_value_search"}
                ]
            )
            return []

        try:
            with psycopg2.connect(host="pgsql", database="app", user="app", password="password", port=5432) as conn:
                with conn.cursor() as cursor:
                    query = """
                        SELECT DISTINCT T.NAME
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND A.DESCRIPTION ILIKE %s
                          AND AV.VALUE ILIKE %s
                        ORDER BY T.NAME;
                    """
                    logger.debug(f"Esecuzione query per attributo: {attribute_desc_raw} e valore: {attribute_val_raw}")
                    cursor.execute(query, (attribute_desc_raw, attribute_val_raw))
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query: {results}")
                    if results:
                        models_list = [r[0] for r in results]
                        formatted_list = "\n".join(f"- {m}" for m in models_list)
                        msg = f"Ecco i modelli che hanno '{attribute_desc_raw}' = '{attribute_val_raw}':\n{formatted_list}"
                        dispatcher.utter_message(text=msg)
                    else:
                        logger.info("Nessun modello trovato per attributo:valore indicato")
                        dispatcher.utter_message(text=f"Nessun modello trovato con '{attribute_desc_raw}' = '{attribute_val_raw}'.")
        except psycopg2.Error as e:
            logger.error(f"Errore in ActionQueryDevicesByAttributeValue2: {e}")
            dispatcher.utter_message(text=f"Errore nel recupero dei dati. DB error: {e}")
        return [FollowupAction("action_reset_slots"), FollowupAction("action_return_menu")]

# ---------- Azioni di Supporto ---------- #

class ActionReturnMenu(Action):
    def name(self) -> str:
        return "action_return_menu"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Ritorno al menù principale.")
        dispatcher.utter_message(
            text="Ciao, sono il tuo assistente personale. Posso esserti utile per:",
            buttons=[
                {"title": "Marche e modelli più venduti", "payload": "/request_brands"},
                {"title": "Caratteristiche tecniche", "payload": "/request_categories"},
                {"title": "Assistenza telefonino", "payload": "/request_assistance"}
            ]
        )
        return []

class ActionResetSlots(Action):
    def name(self) -> str:
        return "action_reset_slots"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Reset degli slot.")
        return [
            SlotSet("brand_raw", None),
            SlotSet("category_raw", None),
            SlotSet("model_raw", None),
            SlotSet("attribute_desc_raw", None),
            SlotSet("attribute_val_raw", None),
        ]

