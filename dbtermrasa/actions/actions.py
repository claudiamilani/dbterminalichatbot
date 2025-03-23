import logging
import difflib
import psycopg2

from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
from rasa_sdk.events import FollowupAction, SlotSet

# ======================= LOGGER SETUP =======================
logger = logging.getLogger(__name__)
logger.setLevel(logging.DEBUG)

# Scrive i log anche su file "bot_debug.log"
file_handler = logging.FileHandler("bot_debug.log")
file_handler.setLevel(logging.DEBUG)

# Formato del log
formatter = logging.Formatter('%(asctime)s - %(levelname)s - %(message)s')
file_handler.setFormatter(formatter)

# Aggiunge l'handler al logger (se non c'è già)
if not logger.handlers:
    logger.addHandler(file_handler)

# Se vuoi anche vedere i log in console, puoi aggiungere uno StreamHandler:
console_handler = logging.StreamHandler()
console_handler.setLevel(logging.DEBUG)
console_handler.setFormatter(formatter)
logger.addHandler(console_handler)


# ====================== ACTIONS =============================

# --------------------- OPZIONE 1: Marche e Modelli (con fuzzy brand) --------------------- #
class ActionQueryBrands(Action):
    def name(self) -> str:
        return "action_query_brands"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        """
        Mostra l'elenco di tutte le marche disponibili.
        """
        logger.info("Esecuzione ActionQueryBrands...")

        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    logger.debug("Eseguo la query per l'elenco di brand...")
                    cursor.execute("""
                        SELECT DISTINCT(V.NAME)
                        FROM TERMINALS T
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND V.PUBLISHED = TRUE
                        ORDER BY V.NAME;
                    """)
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query brand: {results}")

                    if results:
                        brand_list = [r[0] for r in results]
                        formatted = "\n".join(f"- {b}" for b in brand_list)
                        msg = f"Seleziona una marca per visualizzare i modelli:\n{formatted}"
                        dispatcher.utter_message(text=msg)
                        logger.info(f"Inviato elenco brand:\n{msg}")
                    else:
                        msg_no_brands = "Non ci sono marche disponibili al momento."
                        dispatcher.utter_message(text=msg_no_brands)
                        logger.warning(msg_no_brands)

        except psycopg2.Error as e:
            error_msg = f"Errore nel recupero delle marche. DB error: {e}"
            dispatcher.utter_message(text=error_msg)
            logger.error(error_msg)

        return []


class ActionQueryModels(Action):
    def name(self) -> str:
        return "action_query_models"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        """
        Mostra i modelli per una determinata marca (brand_raw).
        Se brand_raw è None o non esiste, fa fuzzy matching.
        Poi resetta gli slot e torna al menù.
        """
        logger.info("Esecuzione ActionQueryModels...")

        brand_raw = tracker.get_slot("brand_raw")
        logger.debug(f"Slot brand_raw: {brand_raw}")

        # Recuperiamo la lista di brand dal DB
        brand_list = []
        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    logger.debug("Eseguo la query per la lista di brand (fuzzy matching)...")
                    cursor.execute("""
                        SELECT DISTINCT(V.NAME)
                        FROM TERMINALS T
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND V.PUBLISHED = TRUE
                        ORDER BY V.NAME;
                    """)
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query brand: {results}")

                    brand_list = [r[0] for r in results]

        except psycopg2.Error as e:
            error_msg = f"Errore nel recupero dei brand. Riprova più tardi. DB error: {e}"
            dispatcher.utter_message(text=error_msg)
            logger.error(error_msg)
            return [
                FollowupAction("action_reset_slots"),
                FollowupAction("utter_greet")
            ]

        if not brand_list:
            msg_no_brands = "Nessuna marca trovata nel database."
            dispatcher.utter_message(text=msg_no_brands)
            logger.warning(msg_no_brands)
            return [
                FollowupAction("action_reset_slots"),
                FollowupAction("utter_greet")
            ]

        # Se brand_raw è None o non presente nella lista, proviamo fuzzy
        user_text = brand_raw if brand_raw else ""
        if not brand_raw or brand_raw.upper() not in [b.upper() for b in brand_list]:
            logger.debug(f"Fuzzy matching per user_text: '{user_text}'")
            best_match = difflib.get_close_matches(user_text, brand_list, n=1, cutoff=0.5)
            logger.debug(f"best_match: {best_match}")

            if best_match:
                matched_brand = best_match[0]
                if matched_brand.upper() != user_text.upper():
                    suggestion_msg = f"Forse cercavi la marca '{matched_brand}'..."
                    dispatcher.utter_message(text=suggestion_msg)
                    logger.info(suggestion_msg)
                brand_raw = matched_brand
            else:
                msg_no_match = f"Non ho trovato nessuna marca simile a '{user_text}'."
                dispatcher.utter_message(text=msg_no_match)
                logger.warning(msg_no_match)
                return [
                    FollowupAction("action_reset_slots"),
                    FollowupAction("utter_greet")
                ]

        # Ora eseguiamo la query dei modelli
        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    logger.debug(f"Eseguo la query modelli per brand: '{brand_raw}'")
                    cursor.execute("""
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
                    """, (brand_raw.upper(),))
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query modelli: {results}")

                    if results:
                        model_list = [r[0] for r in results]
                        formatted_models = "\n".join(f"- {m}" for m in model_list)
                        msg_models = f"Modelli più venduti per {brand_raw.upper()}:\n{formatted_models}"
                        dispatcher.utter_message(text=msg_models)
                        logger.info(msg_models)
                    else:
                        no_models_msg = f"Non ci sono modelli trovati per la marca {brand_raw}."
                        dispatcher.utter_message(text=no_models_msg)
                        logger.warning(no_models_msg)

        except psycopg2.Error as e:
            error_msg = f"Errore nel recupero dei modelli. Riprova più tardi. DB error: {e}"
            dispatcher.utter_message(text=error_msg)
            logger.error(error_msg)

        # Fine -> reset e menù
        return [
            FollowupAction("action_reset_slots"),
            FollowupAction("utter_greet")
        ]


# --------------------- OPZIONE 2: Categorie e Attributi (con fuzzy category) --------------------- #
class ActionQueryCategories(Action):
    def name(self) -> str:
        return "action_query_categories"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryCategories...")

        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    logger.debug("Eseguo la query per l'elenco di categorie...")
                    cursor.execute("""
                        SELECT DISTINCT(AC.NAME)
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND V.PUBLISHED = TRUE
                        ORDER BY AC.NAME;
                    """)
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query categorie: {results}")

                    if results:
                        categories = [r[0] for r in results]
                        formatted = "\n".join(f"- {c}" for c in categories)
                        msg = (f"Ecco le categorie disponibili:\n{formatted}\n\n"
                               "Seleziona una categoria per visualizzare i relativi attributi.")
                        dispatcher.utter_message(text=msg)
                        logger.info(msg)
                    else:
                        no_cat_msg = "Non sono state trovate categorie."
                        dispatcher.utter_message(text=no_cat_msg)
                        logger.warning(no_cat_msg)
        except psycopg2.Error as e:
            error_msg = f"Errore nel recupero delle categorie. Riprova più tardi. DB error: {e}"
            dispatcher.utter_message(text=error_msg)
            logger.error(error_msg)

        return []


class ActionQueryAttributesByCategory(Action):
    def name(self) -> str:
        return "action_query_attributes_by_category"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryAttributesByCategory...")

        category_raw = tracker.get_slot("category_raw")
        logger.debug(f"Slot category_raw: {category_raw}")

        if not category_raw:
            msg_no_cat = "Per favore, specifica prima una categoria."
            dispatcher.utter_message(text=msg_no_cat)
            logger.warning(msg_no_cat)
            return []

        # Recuperiamo la lista di categorie dal DB
        category_list = []
        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    logger.debug("Eseguo la query per l'elenco di categorie (fuzzy matching)...")
                    cursor.execute("""
                        SELECT DISTINCT(AC.NAME)
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND V.PUBLISHED = TRUE
                        ORDER BY AC.NAME;
                    """)
                    results = cursor.fetchall()
                    category_list = [r[0] for r in results]
                    logger.debug(f"category_list: {category_list}")

        except psycopg2.Error as e:
            error_msg = f"Errore nel recupero delle categorie. DB error: {e}"
            dispatcher.utter_message(text=error_msg)
            logger.error(error_msg)
            return []

        # Fuzzy su category_raw
        if category_list and category_raw.upper() not in [c.upper() for c in category_list]:
            logger.debug(f"Fuzzy matching per category: '{category_raw}'")
            best_match = difflib.get_close_matches(category_raw, category_list, n=1, cutoff=0.6)
            logger.debug(f"best_match: {best_match}")
            if best_match:
                matched_cat = best_match[0]
                if matched_cat.upper() != category_raw.upper():
                    suggestion_cat = f"Forse cercavi la categoria '{matched_cat}'..."
                    dispatcher.utter_message(text=suggestion_cat)
                    logger.info(suggestion_cat)
                category_raw = matched_cat
            else:
                msg_no_match = f"Non ho trovato nessuna categoria simile a '{category_raw}'."
                dispatcher.utter_message(text=msg_no_match)
                logger.warning(msg_no_match)
                return []

        # Ora facciamo la query degli attributi
        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    logger.debug(f"Query attributi per category: {category_raw}")
                    cursor.execute("""
                        SELECT DISTINCT A.DESCRIPTION, AV.VALUE
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND V.PUBLISHED = TRUE
                          AND AC.NAME ILIKE %s
                        ORDER BY A.DESCRIPTION;
                    """, (category_raw,))
                    results = cursor.fetchall()
                    logger.debug(f"Risultati attributi: {results}")

                    if results:
                        attributes_list = [f"- {desc}: {val}" for desc, val in results]
                        formatted_attributes = "\n".join(attributes_list)
                        msg = (
                            f"Ecco alcuni attributi per la categoria '{category_raw}':\n{formatted_attributes}\n\n"
                            "Vuoi vedere gli attributi di un modello specifico, "
                            "oppure cercare i modelli che hanno un certo attributo=valore?"
                        )
                        dispatcher.utter_message(
                            text=msg,
                            buttons=[
                                {"title": "Modello specifico", "payload": "/want_model_details"},
                                {"title": "Cerca modelli per attributo", "payload": "/want_attribute_search"}
                            ]
                        )
                        logger.info(msg)
                    else:
                        msg_no_att = f"Non sono stati trovati attributi per la categoria '{category_raw}'."
                        dispatcher.utter_message(text=msg_no_att)
                        logger.warning(msg_no_att)
        except psycopg2.Error as e:
            error_msg = f"Errore nel recupero degli attributi. DB error: {e}"
            dispatcher.utter_message(text=error_msg)
            logger.error(error_msg)

        return []


class ActionQueryAttributesByModel(Action):
    def name(self) -> str:
        return "action_query_attributes_by_model"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryAttributesByModel...")

        category_raw = tracker.get_slot("category_raw")
        model_raw = tracker.get_slot("model_raw")
        logger.debug(f"Slot category_raw: {category_raw}, model_raw: {model_raw}")

        if not category_raw:
            msg_no_cat = "Per favore, specifica prima la categoria."
            dispatcher.utter_message(text=msg_no_cat)
            logger.warning(msg_no_cat)
            return []
        if not model_raw:
            msg_no_model = "Per favore, specifica prima il modello."
            dispatcher.utter_message(text=msg_no_model)
            logger.warning(msg_no_model)
            return []

        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    logger.debug(f"Query attributi per model '{model_raw}' e category '{category_raw}'")
                    cursor.execute("""
                        SELECT DISTINCT A.DESCRIPTION, AV.VALUE
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND V.PUBLISHED = TRUE
                          AND AC.NAME ILIKE %s
                          AND T.NAME ILIKE %s
                        ORDER BY A.DESCRIPTION;
                    """, (category_raw, f"%{model_raw}%"))
                    results = cursor.fetchall()
                    logger.debug(f"Risultati attributi modello: {results}")

                    if results:
                        attributes_list = [f"- {desc}: {val}" for desc, val in results]
                        formatted_attributes = "\n".join(attributes_list)
                        msg = (
                            f"Ecco gli attributi per il modello '{model_raw}' "
                            f"(categoria: {category_raw}):\n{formatted_attributes}"
                        )
                        dispatcher.utter_message(text=msg)
                        logger.info(msg)
                    else:
                        msg_no_att = (
                            f"Non sono stati trovati attributi per il modello '{model_raw}' "
                            f"nella categoria '{category_raw}'."
                        )
                        dispatcher.utter_message(text=msg_no_att)
                        logger.warning(msg_no_att)

        except psycopg2.Error as e:
            error_msg = f"Errore nel recupero degli attributi del modello. DB error: {e}"
            dispatcher.utter_message(text=error_msg)
            logger.error(error_msg)

        # Fine -> reset e menù
        return [
            FollowupAction("action_reset_slots"),
            FollowupAction("utter_greet")
        ]


class ActionQueryDevicesByAttributeValue(Action):
    def name(self) -> str:
        return "action_query_devices_by_attribute_value"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionQueryDevicesByAttributeValue...")

        attr_description = tracker.get_slot("attribute_desc_raw")
        attr_value = tracker.get_slot("attribute_val_raw")
        logger.debug(f"Slot attribute_desc_raw: {attr_description}, attribute_val_raw: {attr_value}")

        if not attr_description or not attr_value:
            msg_slot_missing = "Per favore, specifica l'attributo e il valore che vuoi ricercare."
            dispatcher.utter_message(text=msg_slot_missing)
            logger.warning(msg_slot_missing)
            return []

        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    logger.debug(f"Query modelli con attributo='{attr_description}' e valore='{attr_value}'")
                    cursor.execute("""
                        SELECT DISTINCT T.NAME
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND V.PUBLISHED = TRUE
                          AND A.DESCRIPTION ILIKE %s
                          AND AV.VALUE ILIKE %s
                        ORDER BY T.NAME;
                    """, (attr_description, attr_value))
                    results = cursor.fetchall()
                    logger.debug(f"Risultati query attributo=valore: {results}")

                    if results:
                        models_list = [r[0] for r in results]
                        formatted_list = "\n".join(f"- {m}" for m in models_list)
                        msg_found = (
                            f"Ecco i modelli che hanno '{attr_description}' = '{attr_value}':\n{formatted_list}"
                        )
                        dispatcher.utter_message(text=msg_found)
                        logger.info(msg_found)
                    else:
                        msg_not_found = f"Non ho trovato modelli con '{attr_description}' = '{attr_value}'."
                        dispatcher.utter_message(text=msg_not_found)
                        logger.warning(msg_not_found)

        except psycopg2.Error as e:
            error_msg = f"Errore nel recupero dei dati. DB error: {e}"
            dispatcher.utter_message(text=error_msg)
            logger.error(error_msg)

        # Fine -> reset e menù
        return [
            FollowupAction("action_reset_slots"),
            FollowupAction("utter_greet")
        ]


# --------------------- AZIONI DI SUPPORTO --------------------- #
class ActionAskAfterCategory(Action):
    def name(self) -> str:
        return "action_ask_after_category"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionAskAfterCategory...")

        msg = "Vuoi un modello specifico o cercare i modelli per attributo=valore?"
        dispatcher.utter_message(
            text=msg,
            buttons=[
                {"title": "Modello specifico", "payload": "/want_model_details"},
                {"title": "Cerca modelli per attributo", "payload": "/want_attribute_search"}
            ]
        )
        logger.debug(msg)
        return []


class ActionReturnMenu(Action):
    def name(self) -> str:
        return "action_return_menu"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionReturnMenu: torno al menù iniziale.")
        return [FollowupAction("utter_greet")]


class ActionResetSlots(Action):
    def name(self) -> str:
        return "action_reset_slots"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        logger.info("Esecuzione ActionResetSlots: pulisco gli slot principali...")

        return [
            SlotSet("brand_raw", None),
            SlotSet("category_raw", None),
            SlotSet("model_raw", None),
            SlotSet("attribute_desc_raw", None),
            SlotSet("attribute_val_raw", None),
        ]

