---
description: Standards for JavaScript in app-votes
---
# JavaScript Logic & API Interaction Standards

## 1. API Interaction (`fetchData`)
All authenticated API calls MUST use the global `fetchData` helper found in `functions_admin.js`.
This helper handles:
- Authorization Header (Bearer Token).
- JSON Parsing.
- Basic Error Handling (401 Unauthorized, Network Errors).

**Usage:**
```javascript
// GET
const data = await fetchData(BASE_URL_API + '/controller/method');

// POST (FormData)
let formData = new FormData();
formData.append('key', 'value');
const data = await fetchData(BASE_URL_API + '/controller/method', 'POST', formData);

// POST (JSON)
let jsonData = { key: 'value' };
const data = await fetchData(BASE_URL_API + '/controller/method', 'POST', jsonData);
```

**Return Value:**
`fetchData` always returns a JSON object (if successful parse) or an object `{status: false, msg: "Error..."}` if it fails.
Always check `if (data && data.status)` before proceeding.

## 2. DataTables Configuration (`getDataTableFetchConfig`)
All DataTables that load data from the API MUST use the `getDataTableFetchConfig` helper.
This ensures the JWT token is passed correctly to the server-side processing script.

**Usage:**
```javascript
$('#tableId').DataTable({
    "processing": true,
    "language": lenguajeEspanol,
    ...getDataTableFetchConfig('/Controller/Method', { extraParam: 'value' }),
    "columns": [ ... ]
});
```

## 3. General Structure
- Wrap initialization logic in `document.addEventListener('DOMContentLoaded', ...)`
- Use `async/await` for asynchronous operations instead of callback hell or raw promises where possible.
- Keep UI logic separated from Data logic.
