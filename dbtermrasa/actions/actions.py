from rasa_sdk import Action, Tracker
from rasa_sdk.executor import CollectingDispatcher
import psycopg2
from psycopg2 import sql

class ActionQueryBrands(Action):
    def name(self) -> str:
        return "action_query_brands"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        try:
            # Connessione al database PostgreSQL
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    cursor.execute("""
                        SELECT upper(device_make)
                        FROM public."DWH_TRASPOSTA"
                        WHERE dcr_status = 'Mobilethink verified, GSMA and external data'
                        AND TO_DATE(allocation_date, 'DD-Mon-YYYY') >= TO_DATE('2023-01-01', 'YYYY-MM-DD')
                        GROUP BY upper(device_make)
                        ORDER BY count(tac) desc
                        LIMIT 15;
                    """)

                    # Fetch all brands
                    brands_query_results = cursor.fetchall()
                    brand_list = [brand[0] for brand in brands_query_results]

                    # Formatta le marche come lista Markdown
                    formatted_brands = "\n".join([f"- {brand}" for brand in brand_list])
                    dispatcher.utter_message(text=f"Seleziona una marca per visualizzare i modelli più venduti dal 2023:\n{formatted_brands}")

        except psycopg2.Error as e:
            # Gestisci eventuali errori di connessione al database o query
            dispatcher.utter_message(text="Errore nel recupero delle marche. Riprova più tardi.")
            print(f"Database error: {e}")
        return []

class ActionQueryModels(Action):
    def name(self) -> str:
        return "action_query_models"

    def run(self, dispatcher: CollectingDispatcher, tracker: Tracker, domain: dict):
        # Estrai la marca scelta dall'utente
        device_make = tracker.get_slot("device_make")

        if not device_make:
            # Se non è stato selezionato un brand, inviamo un messaggio appropriato
            dispatcher.utter_message(text="Non è stata selezionata alcuna marca. Scegli prima una marca.")
            return []

        try:
            # Connessione al database PostgreSQL
            with psycopg2.connect(
                host="pgsql",
                database="app",
                user="app",
                password="password",
                port=5432
            ) as conn:
                with conn.cursor() as cursor:
                    cursor.execute("""
                        SELECT device_model
                        FROM public."DWH_TRASPOSTA"
                        WHERE upper(device_make) = %s
                        AND dcr_status = 'Mobilethink verified, GSMA and external data'
                        AND TO_DATE(allocation_date, 'DD-Mon-YYYY') >= TO_DATE('2023-01-01', 'YYYY-MM-DD')
                        GROUP BY device_model
                        ORDER BY COUNT(tac) DESC
                        LIMIT 15;
                    """, (device_make.upper(),))

                    # Fetch all models
                    models_query_results = cursor.fetchall()
                    model_list = [model[0] for model in models_query_results]

                    # Rispondi con i modelli più venduti come lista Markdown
                    if model_list:
                        formatted_models = "\n".join([f"- {model}" for model in model_list])
                        dispatcher.utter_message(text=f"Modelli più venduti per {device_make} dal 2023:\n{formatted_models}")
                    else:
                        dispatcher.utter_message(text=f"Non ci sono modelli trovati per la marca {device_make}. Riprova più tardi.")

        except psycopg2.Error as e:
            # Gestisci eventuali errori di connessione al database o query
            dispatcher.utter_message(text="Errore nel recupero dei modelli. Riprova più tardi.")
            print(f"Database error: {e}")

        return []

