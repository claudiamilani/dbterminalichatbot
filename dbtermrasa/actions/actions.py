import logging
import difflib
import psycopg2
from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
from rasa_sdk.events import FollowupAction, SlotSet

# Logger setup
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

# === Utility Function ===
def db_connect():
    return psycopg2.connect(host="pgsql", database="app", user="app", password="password", port=5432)

# === Azioni ===

class ActionQueryBrands(Action):
    def name(self): return "action_query_brands"

    def run(self, dispatcher, tracker, domain):
        logger.info("Esecuzione ActionQueryBrands...")
        try:
            with db_connect() as conn:
                with conn.cursor() as cursor:
                    query = '''
                        SELECT DISTINCT(V.NAME)
                        FROM TERMINALS T
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE AND T.CERTIFIED = TRUE AND V.PUBLISHED = TRUE
                        ORDER BY V.NAME;
                    '''
                    logger.debug(f"Query marche: {query}")
                    cursor.execute(query)
                    brands = cursor.fetchall()
                    logger.debug(f"Risultati marche: {brands}")
                    if brands:
                        msg = "Seleziona una marca per visualizzare i modelli:\n" + "\n".join(f"- {b[0]}" for b in brands)
                        dispatcher.utter_message(text=msg)
                    else:
                        dispatcher.utter_message(text="Non ci sono marche disponibili.")
        except Exception as e:
            logger.exception("Errore ActionQueryBrands")
            dispatcher.utter_message(text=f"Errore DB: {e}")
        return []

class ActionQueryModels(Action):
    def name(self): return "action_query_models"

    def run(self, dispatcher, tracker, domain):
        logger.info("Esecuzione ActionQueryModels...")
        brand_raw = tracker.get_slot("brand_raw")
        logger.debug(f"Slot brand_raw: {brand_raw}")

        try:
            with db_connect() as conn:
                with conn.cursor() as cursor:
                    query_brands = '''
                        SELECT DISTINCT(V.NAME)
                        FROM TERMINALS T
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE AND T.CERTIFIED = TRUE AND V.PUBLISHED = TRUE
                        ORDER BY V.NAME;
                    '''
                    cursor.execute(query_brands)
                    brand_list = [r[0] for r in cursor.fetchall()]
                    logger.debug(f"Brand disponibili: {brand_list}")
        except Exception as e:
            logger.exception("Errore recupero marche")
            dispatcher.utter_message(text=f"Errore DB: {e}")
            return []

        if not brand_raw or brand_raw.upper() not in [b.upper() for b in brand_list]:
            best = difflib.get_close_matches(brand_raw or "", brand_list, n=1, cutoff=0.5)
            if best:
                brand_raw = best[0]
                dispatcher.utter_message(text=f"Forse cercavi: {brand_raw}")
                return []
            else:
                dispatcher.utter_message(text="Marca non trovata. Prova a sceglierne un'altra dicendo ad esempio: 'Vorrei vedere i modelli di MARCA'.")
                return []

        try:
            with db_connect() as conn:
                with conn.cursor() as cursor:
                    query_models = '''
                        SELECT T.NAME
                        FROM TERMINALS T
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        JOIN TACS TA ON TA.TERMINAL_ID = T.ID
                        WHERE T.PUBLISHED = TRUE AND T.CERTIFIED = TRUE AND V.PUBLISHED = TRUE
                          AND V.NAME ILIKE %s
                        GROUP BY T.NAME
                        ORDER BY COUNT(TA.VALUE) DESC
                        LIMIT 15;
                    '''
                    logger.debug(f"Query modelli per brand: {query_models} | Param: {brand_raw}")
                    cursor.execute(query_models, (brand_raw,))
                    models = cursor.fetchall()
                    logger.debug(f"Modelli trovati: {models}")
                    if models:
                        msg = f"Modelli più venduti per {brand_raw}:\n" + "\n".join(f"- {m[0]}" for m in models)
                        dispatcher.utter_message(text=msg)
                        return [FollowupAction("action_return_menu")]
                    else:
                        dispatcher.utter_message(text=f"Nessun modello trovato per '{brand_raw}'. Puoi scrivere ad esempio: 'Vorrei vedere i modelli di Samsung'.")
                        return []
        except Exception as e:
            logger.exception("Errore modelli")
            dispatcher.utter_message(text=f"Errore DB: {e}")
        return []

class ActionQueryCategories(Action):
    def name(self): return "action_query_categories"

    def run(self, dispatcher, tracker, domain):
        logger.info("Esecuzione ActionQueryCategories...")
        try:
            with db_connect() as conn:
                with conn.cursor() as cursor:
                    query = '''
                        SELECT DISTINCT(AC.NAME)
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        WHERE T.PUBLISHED = TRUE AND T.CERTIFIED = TRUE AND A.PUBLISHED = TRUE
                        ORDER BY AC.NAME;
                    '''
                    logger.debug(f"Query categorie: {query}")
                    cursor.execute(query)
                    cats = cursor.fetchall()
                    logger.debug(f"Categorie trovate: {cats}")
                    if cats:
                        msg = "Ecco le categorie disponibili:\n" + "\n".join(f"- {c[0]}" for c in cats)
                        dispatcher.utter_message(text=msg)
                    else:
                        dispatcher.utter_message(text="Nessuna categoria trovata.")
        except Exception as e:
            logger.exception("Errore categorie")
            dispatcher.utter_message(text=f"Errore DB: {e}")
        return []

class ActionQueryAttributesByCategory(Action):
    def name(self): return "action_query_attributes_by_category"

    def run(self, dispatcher, tracker, domain):
        logger.info("Esecuzione ActionQueryAttributesByCategory...")
        category_raw = tracker.get_slot("category_raw")
        logger.debug(f"Categoria ricevuta: {category_raw}")
        if category_raw and len(category_raw.split()) > 1:
            category_raw = category_raw.split()[-1]
            logger.debug(f"Fallback a: {category_raw}")

        if not category_raw:
            dispatcher.utter_message(text="Specifica la categoria.")
            return []

        try:
            with db_connect() as conn:
                with conn.cursor() as cursor:
                    query = '''
                        SELECT DISTINCT A.DESCRIPTION, AV.VALUE
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        WHERE T.PUBLISHED = TRUE AND T.CERTIFIED = TRUE AND A.PUBLISHED = TRUE
                          AND AC.NAME ILIKE %s
                        ORDER BY A.DESCRIPTION;
                    '''
                    logger.debug(f"Query attributi per categoria: {query} | Param: {category_raw}")
                    cursor.execute(query, (category_raw,))
                    results = cursor.fetchall()
                    logger.debug(f"Attributi trovati: {results}")
                    if results:
                        msg = f"Ecco gli attributi per '{category_raw}':\n" + "\n".join(f"- {desc}: {val}" for desc, val in results)
                        dispatcher.utter_message(text=msg)

                        dispatcher.utter_message(
                            text="Vuoi continuare con una ricerca specifica?",
                            buttons=[
                                {"title": "Attributi per categoria per modello", "payload": "/model_category_search_prompted"},
                                {"title": "Cercare modelli per attributo", "payload": "/attribute_value_search_prompted"},
                            ]
                        )
                    else:
                        dispatcher.utter_message(
                            text=f"Nessun dato trovato per '{category_raw}'. Puoi scrivere ad esempio: 'Vorrei vedere gli attributi di cpu' oppure 'Vorrei vedere i modelli di OPPO'."
                        )
        except Exception as e:
            logger.exception("Errore attributi per categoria")
            dispatcher.utter_message(text=f"Errore DB: {e}")
        return []

class ActionQueryAttributesByModelAndCategory(Action):
    def name(self): return "action_query_attributes_by_model_and_category"

    def run(self, dispatcher, tracker, domain):
        logger.info("Esecuzione ActionQueryAttributesByModelAndCategory...")
        text = tracker.latest_message.get("text", "").strip()
        logger.debug(f"Testo ricevuto: {text}")

        if not text or "Ricerca_attributi:" not in text:
            dispatcher.utter_message(text="Per favore scrivi nel formato: 'Ricerca_attributi: modello|categoria'")
            return []

        text = text.replace("Ricerca_attributi:", "").strip()

        if "|" not in text:
            dispatcher.utter_message(text="Formato riconosciuto: 'Ricerca_attributi: modello|categoria'")
            return []

        model_raw, category_raw = [p.strip() for p in text.split("|", 1)]

        try:
            with db_connect() as conn:
                with conn.cursor() as cursor:
                    query = '''
                        SELECT DISTINCT A.DESCRIPTION, AV.VALUE
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        WHERE T.PUBLISHED = TRUE AND T.CERTIFIED = TRUE AND A.PUBLISHED = TRUE
                          AND AC.NAME ILIKE %s AND T.NAME ILIKE %s
                        ORDER BY A.DESCRIPTION;
                    '''
                    logger.debug(f"Query per modello e categoria: {query} | Param: {category_raw}, %{model_raw}%")
                    cursor.execute(query, (category_raw, f"%{model_raw}%"))
                    results = cursor.fetchall()
                    logger.debug(f"Attributi trovati: {results}")
                    if results:
                        msg = f"Ecco gli attributi per il modello '{model_raw}' (categoria: {category_raw}):\n" + "\n".join(f"- {desc}: {val}" for desc, val in results)
                        dispatcher.utter_message(text=msg)
                    else:
                        dispatcher.utter_message(text=f"Nessun attributo trovato per '{model_raw}' nella categoria '{category_raw}'.")
        except Exception as e:
            logger.exception("Errore attributi per modello+categoria")
            dispatcher.utter_message(text=f"Errore DB: {e}")

        dispatcher.utter_message(
            text="Vuoi continuare la ricerca o tornare al menù?",
            buttons=[
                {"title": "Continua ricerca", "payload": "/continue_research_menu"},
                {"title": "Menù principale", "payload": "/return_to_menu"}
                    ]   
                )

        return [FollowupAction("action_reset_slots")]
class ActionQueryDevicesByAttributeValue2(Action):
    def name(self): return "action_query_devices_by_attribute_value_2"

    def run(self, dispatcher, tracker, domain):
        logger.info("Esecuzione ActionQueryDevicesByAttributeValue2...")
        text = tracker.latest_message.get("text", "").strip()

        if not text or "Ricerca_modelli:" not in text:
            dispatcher.utter_message(text="Per favore scrivi nel formato: 'Ricerca_modelli: attributo|valore'")
            return []

        text = text.replace("Ricerca_modelli:", "").strip()

        if "|" not in text:
            dispatcher.utter_message(text="Formato riconosciuto: 'Ricerca_modelli: attributo|valore'")
            return []

        attribute_desc_raw, attribute_val_raw = [p.strip() for p in text.split("|", 1)]

        try:
            with db_connect() as conn:
                with conn.cursor() as cursor:
                    query = '''
                        SELECT DISTINCT T.NAME
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        WHERE T.PUBLISHED = TRUE AND T.CERTIFIED = TRUE AND A.PUBLISHED = TRUE
                          AND A.DESCRIPTION ILIKE %s AND AV.VALUE ILIKE %s
                        ORDER BY T.NAME;
                    '''
                    logger.debug(f"Query per attributo=valore: {query} | Param: {attribute_desc_raw}, {attribute_val_raw}")
                    cursor.execute(query, (attribute_desc_raw, attribute_val_raw))
                    results = cursor.fetchall()
                    logger.debug(f"Modelli trovati: {results}")
                    if results:
                        models = "\n".join(f"- {r[0]}" for r in results)
                        dispatcher.utter_message(text=f"Ecco i modelli con '{attribute_desc_raw}' = '{attribute_val_raw}':\n{models}")
                    else:
                        dispatcher.utter_message(text=f"Nessun modello trovato con '{attribute_desc_raw}' = '{attribute_val_raw}'.")
        except Exception as e:
            logger.exception("Errore attributi=valore")
            dispatcher.utter_message(text=f"Errore DB: {e}")

        dispatcher.utter_message(
        text="Vuoi continuare la ricerca o tornare al menù?",
        buttons=[
            {"title": "Continua ricerca", "payload": "/continue_research_menu"},
            {"title": "Menù principale", "payload": "/return_to_menu"}
                ]
            )

        return [FollowupAction("action_reset_slots")]

class ActionResetSlots(Action):
    def name(self): return "action_reset_slots"

    def run(self, dispatcher, tracker, domain):
        logger.info("Reset degli slot.")
        return [
            SlotSet("brand_raw", None),
            SlotSet("category_raw", None),
            SlotSet("model_raw", None),
            SlotSet("attribute_desc_raw", None),
            SlotSet("attribute_val_raw", None),
            SlotSet("model_category_search_raw", None),
            SlotSet("attribute_value_search_raw", None),
        ]

class ActionReturnMenu(Action):
    def name(self): return "action_return_menu"

    def run(self, dispatcher, tracker, domain):
        logger.info("Ritorno al menu principale.")
        dispatcher.utter_message(
            text="Ciao, sono il tuo assistente personale. Posso esserti utile per:",
            buttons=[
                {"title": "Marche e modelli più venduti", "payload": "/request_brands"},
                {"title": "Caratteristiche tecniche", "payload": "/request_categories"},
                {"title": "Assistenza telefonino", "payload": "/request_assistance"}
            ]
        )
        return []

class ActionOpenLink(Action):
    def name(self): return "open_link"

    def run(self, dispatcher, tracker, domain):
        link = next(tracker.get_latest_entity_values("link"), None)
        if link:
            # Apri il popup
            dispatcher.utter_message(
                json_message={"type": "popup", "link": link}
            )

            # Mostra i 3 bottoni: Apple, Android, Menu
            dispatcher.utter_message(
                text="Hai bisogno di ulteriore assistenza?",
                buttons=[
                    {"title": "APPLE", "payload": "/assistenza_apple"},
                    {"title": "ANDROID", "payload": "/assistenza_android"},
                    {"title": "TORNA AL MENU", "payload": "/return_to_menu"}
                ]
            )
        else:
            dispatcher.utter_message(text="Link non disponibile.")
        return []

