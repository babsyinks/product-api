# Introduction

A clean RESTful API for managing products, built with Laravel.

<aside>
    <strong>Base URL</strong>: <code>http://localhost:8000</code>
</aside>

## Overview

This API provides full CRUD management for the **Product** resource, with optional Sanctum token authentication.

### Base URL
```
http://localhost:8000/api/v1
```

### Response format
All responses are JSON. Successful responses follow:
```json
{ "data": { ... } }          // single resource
{ "data": [...], "meta": {} } // collection with pagination
```

### Error format
```json
{ "message": "...", "errors": { "field": ["..."] } }
```

### Auth
Public endpoints: `GET /products`, `GET /products/{id}`
Protected endpoints (require Bearer token): `POST`, `PUT`, `DELETE`

