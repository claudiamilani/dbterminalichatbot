# dbterminalichatbot
- app ->>  contiene il codice della console di amministrazione: si basa su lft Copyright (c) 2024. Medialogic S.p.A. La dir di sviluppo della console di amministrazione è app/DBT
- dbtermrasa ->> installazione e configurazione di rasa. Nella dir actions è presente il coding Python per la connessione al database 
- rasa-wechat ->> frontend chatbot di assistenza

---

## Requisiti
- [docker.desktop](https://docs.docker.com/desktop/setup/install/windows-install/)
- [pgAdmin 4](https://www.pgadmin.org/download/)


1. **Clona il repository:**

   ```bash
   git clone https://github.com/claudiamilani/dbterminalichatbot.git
   cd dbterminalichatbot
   ```

2. **Avvia i servizi con Docker Compose:**
   
   avvia docker.desktop

   ```bash da wsl nella dir dbterminalichatbot
   docker-compose up --build -d
   ```


3. **Connessione al database PostgreSQL

	a. Apri pgAdmin  
	b. Clic destro su **Servers > Register > Server…**
	c. **Scheda “General”**  
   		- **Name:** `dbterminali`  

	d. **Scheda “Connection”**
   		- **Host name/address:**  
     		- Se usi pgAdmin localmente: `localhost`
   		- **Port:** `5432`
   		- **Username:** `app`
   		- **Password:** `password`

	e. Clicca su **Save**

4. Importazione del backup `.backup.gz`

	a. Clic destro su `Databases` > **Create > Database**
   		- **Name:** `app` (deve corrispondere a quello usato nell’app)

	b. Una volta creato il database `app`, clic destro su di esso > **Restore**

	c. Nella finestra di ripristino:
   		- **Format:** `Custom or tar`
   		- **Filename:** seleziona `basic_dbtermchatbot23022025.backup.gz` (decomprimilo se necessario)

	d. Clicca **Restore** e attendi il completamento

	PS: con pgAdmin il restore fallisce per pg_restore: warning: errors ignored on restore: 2 ma sono solo warning da ignorare. 

5. Accedere alla console di amministrazione:

	https://localhost/ (admin M3dialogic$ )

	chatbot web:

	http://localhost:8080/
