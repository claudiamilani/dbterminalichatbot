from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
import psycopg2

# --------------------- OPZIONE 1: Marche e Modelli --------------------- #
class ActionQueryBrands(Action):
    def name(self) -> str:
        return "action_query_brands"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    cursor.execute("""
                        SELECT DISTINCT(V.NAME)
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND V.PUBLISHED = TRUE;
                    """)
                    results = cursor.fetchall()
                    brand_list = [r[0] for r in results]
                    formatted_brands = "\n".join(f"- {b}" for b in brand_list)
                    dispatcher.utter_message(
                        text=f"Seleziona una marca per visualizzare i modelli:\n{formatted_brands}"
                    )
        except psycopg2.Error as e:
            dispatcher.utter_message(text="Errore nel recupero delle marche. Riprova più tardi.")
            print(f"Database error: {e}")

        return []


class ActionQueryModels(Action):
    def name(self) -> str:
        return "action_query_models"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        device_make = tracker.get_slot("device_make")
        if not device_make:
            dispatcher.utter_message(text="Non è stata selezionata alcuna marca. Scegli prima una marca.")
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
                    cursor.execute("""
                        SELECT T.NAME
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        JOIN TACS TA ON TA.TERMINAL_ID = T.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE
                          AND V.NAME = %s
                        GROUP BY T.NAME
                        ORDER BY COUNT(TA.VALUE) DESC
                        LIMIT 15;
                    """, (device_make.upper(),))
                    results = cursor.fetchall()
                    model_list = [r[0] for r in results]

                    if model_list:
                        formatted_models = "\n".join(f"- {m}" for m in model_list)
                        dispatcher.utter_message(
                            text=f"Modelli più venduti per {device_make.upper()}:\n{formatted_models}"
                        )
                    else:
                        dispatcher.utter_message(
                            text=f"Non ci sono modelli trovati per la marca {device_make}."
                        )
        except psycopg2.Error as e:
            dispatcher.utter_message(text="Errore nel recupero dei modelli. Riprova più tardi.")
            print(f"Database error: {e}")

        return []

# --------------------- OPZIONE 2: Categorie e Attributi --------------------- #
class ActionQueryCategories(Action):
    def name(self) -> str:
        return "action_query_categories"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        """
        Mostra l'elenco di tutte le categorie (AC.NAME) pubblicate e certificate
        """
        try:
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    cursor.execute("""
                        SELECT DISTINCT(AC.NAME)
                        FROM TERMINALS T
                        JOIN ATTRIBUTE_VALUES AV ON T.ID = AV.TERMINAL_ID
                        JOIN DBT_ATTRIBUTES A ON AV.DBT_ATTRIBUTE_ID = A.ID
                        JOIN ATTR_CATEGORIES AC ON A.ATTR_CATEGORY_ID = AC.ID
                        JOIN VENDORS V ON T.VENDOR_ID = V.ID
                        WHERE T.PUBLISHED = TRUE
                          AND T.CERTIFIED = TRUE
                          AND A.PUBLISHED = TRUE;
                    """)
                    results = cursor.fetchall()
                    categories = [r[0] for r in results]
                    if categories:
                        formatted_categories = "\n".join(f"- {c}" for c in categories)
                        dispatcher.utter_message(
                            text=f"Ecco le categorie disponibili:\n{formatted_categories}\n\n"
                                 f"Scegli una categoria per vedere i relativi attributi."
                        )
                    else:
                        dispatcher.utter_message(text="Non sono state trovate categorie.")
        except psycopg2.Error as e:
            dispatcher.utter_message(text="Errore nel recupero delle categorie. Riprova più tardi.")
            print(f"Database error: {e}")

        return []


class ActionQueryAttributesByCategory(Action):
    def name(self) -> str:
        return "action_query_attributes_by_category"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        """
        Mostra l'elenco (descrizione, valore) degli attributi per una determinata categoria.
        Esempio di query per 'display' con ingestion_source_id = 2 (se necessario).
        """
        category = tracker.get_slot("attribute_category")
        if not category:
            dispatcher.utter_message(text="Per favore, specifica prima una categoria.")
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
                          AND AC.NAME = %s
                          AND AV.INGESTION_SOURCE_ID = 2
                        ORDER BY A.DESCRIPTION;
                    """, (category,))
                    results = cursor.fetchall()

                    if results:
                        # Creiamo una lista tipo: "DESCRIZIONE: VALORE"
                        attributes_list = [f"- {desc}: {val}" for desc, val in results]
                        formatted_attributes = "\n".join(attributes_list)
                        dispatcher.utter_message(
                            text=f"Ecco alcuni attributi per la categoria '{category}':\n{formatted_attributes}\n\n"
                                 f"Se vuoi vedere gli attributi di un modello specifico, dimmelo (es. 'Voglio i dettagli per A57s')."
                        )
                    else:
                        dispatcher.utter_message(
                            text=f"Non sono stati trovati attributi per la categoria '{category}'."
                        )
        except psycopg2.Error as e:
            dispatcher.utter_message(text="Errore nel recupero degli attributi. Riprova più tardi.")
            print(f"Database error: {e}")

        return []


class ActionQueryAttributesByModel(Action):
    def name(self) -> str:
        return "action_query_attributes_by_model"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        """
        Mostra descrizione e valore per un modello specifico (es. A57s) all’interno di una certa categoria.
        """
        category = tracker.get_slot("attribute_category")
        device_model = tracker.get_slot("device_model")

        if not category:
            dispatcher.utter_message(text="Per favore, specifica prima la categoria.")
            return []
        if not device_model:
            dispatcher.utter_message(text="Per favore, specifica prima il modello.")
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
                          AND AC.NAME = %s
                          AND AV.INGESTION_SOURCE_ID = 2
                          AND T.NAME ILIKE %s
                        ORDER BY A.DESCRIPTION;
                    """, (category, f"%{device_model}%"))
                    results = cursor.fetchall()

                    if results:
                        attributes_list = [f"- {desc}: {val}" for desc, val in results]
                        formatted_attributes = "\n".join(attributes_list)
                        dispatcher.utter_message(
                            text=f"Ecco gli attributi per il modello '{device_model}' (categoria: {category}):\n"
                                 f"{formatted_attributes}"
                        )
                    else:
                        dispatcher.utter_message(
                            text=f"Non sono stati trovati attributi per il modello '{device_model}' "
                                 f"nella categoria '{category}'."
                        )
        except psycopg2.Error as e:
            dispatcher.utter_message(text="Errore nel recupero degli attributi del modello. Riprova più tardi.")
            print(f"Database error: {e}")

        return []

