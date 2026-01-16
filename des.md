Walkthrough: Vote System Mobile App & Public API
This walkthrough documents the implementation of the public API endpoints and the hybrid mobile application (app-movil) which includes both public voting information and a restricted administrative module for E-14 reporting.

1. Public API Implementation (api-votes)
We enabled public access to specific endpoints to allow voters to consult their polling station and register their vote without requiring JWT authentication.

Key Changes
Controller: Created 
Publico.php
 to handle 
consultarPuesto
 and 
registrarVoto
.
Model: Updated 
PublicoModel.php
.
Fixed SQL Joins to correctly retrieve polling station details from the denormalized places table via mesas and puestos.
Implemented Hot Registration: If a voter exists in the census (places) but not in the local database (electores), they are automatically registered upon voting.
2. Mobile Application (app-movil)
We built a lightweight, responsive SPA (Single Page Application) using Bootstrap 5 and Vanilla JS.

Features
A. Public Section
Home: Simple navigation to "Where do I vote?" and "I Voted!".
Consultation: Search by ID (Cédula) to find polling station details.
Vote Registration: Confirmation logic with "Hot Registration" support.
Config: Dynamic API_URL configuration in 
js/config.js
 to switch between Local/Dev/Prod.
B. Administrative Section (Native)
Instead of redirecting to the desktop web app, we implemented a fully native experience for witnesses/admins.

Native Login:

Accessible via "Admin" button in Navbar.
Authenticates against api-votes/Login.
Creates a session bridge to the main app if needed, but primarily uses JWT for API access.
Fix: Modified 
Login.php
 to allow 
crearSesion
 requests even if a session already exists.
Native E-14 Module:

View: A dedicated "Resultados E-14" view within the mobile app.
Cascading Selectors: Department -> Municipality -> Zone -> Puesto -> Mesa. Only loads relevant data.
Validation: Checks if a table (mesa) has already reported results before allowing data entry.
Candidate Loading: Dynamically fetches active candidates and builds the input form.
Submission: Sends results to api-votes/Resultados/setE14 securely.
3. Verification & Testing
Manual Testing
Browser: Verified full flow (Consult -> Vote -> Login -> E-14) in Chrome/Edge mobile emulation.
Physical Device: configured 
config.js
 with local IP (192.168.1.5) to allow testing on a real smartphone connected to the same Wi-Fi.
Artifacts Created
app-movil/index.html
: Main UI.
app-movil/js/app.js
: Application Logic.
app-movil/js/config.js
: Environment configuration.
app-movil/css/styles.css
: Custom styling.
4. Next Steps (Deployment)
Hosting: To test "on the street", the API must be hosted on a public server (api-votes.com or similar).
APK: The app can be converted to an APK (using Capacitor/Cordova) or used as a PWA.