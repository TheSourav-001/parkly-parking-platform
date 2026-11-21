# Parkly — Google OAuth 2.0 Developer Setup Guide

This guide explains how to configure Google OAuth 2.0 credentials for Parkly development and production environments.

---

## 1. Google Cloud Console Setup

1. Go to the [Google Cloud Console](https://console.cloud.google.com/).
2. Create a new project or select an existing project (e.g. `Parkly`).
3. Navigate to **APIs & Services** > **OAuth consent screen**:
   - Choose **External** user type and click **Create**.
   - Fill in the required application details:
     - **App name**: `Parkly`
     - **User support email**: Your developer email
     - **Developer contact information**: Your developer email
   - Click **Save and Continue**.
4. **Scopes**:
   - Add the following standard scopes:
     - `.../auth/userinfo.email`
     - `.../auth/userinfo.profile`
     - `openid`
   - Click **Save and Continue**.
5. **Test users** (if app is in "Testing" mode):
   - Add your test Google accounts to allow login during development.

---

## 2. Create OAuth 2.0 Client Credentials

1. Navigate to **APIs & Services** > **Credentials**.
2. Click **Create Credentials** > **OAuth client ID**.
3. Select **Application type**: `Web application`.
4. Name: `Parkly Local Development`.
5. **Authorized JavaScript origins**:
   - `http://localhost:5173` (Frontend Vue SPA)
   - `http://localhost:8000` (Backend Laravel API)
6. **Authorized redirect URIs**:
   - `http://localhost:8000/auth/google/callback`
7. Click **Create**.
8. Copy the **Client ID** and **Client Secret**.

---

## 3. Backend Environment Configuration

Open `backend/.env` (never commit real credentials to Git) and add:

```env
GOOGLE_CLIENT_ID=your_actual_google_client_id_here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_actual_google_client_secret_here
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

Ensure `FRONTEND_URL` is set to your active Vue development server:
```env
FRONTEND_URL=http://localhost:5173
```

> **IMPORTANT**:
> - Never commit `.env` or real client secrets to version control.
> - Ensure both frontend and backend use the `localhost` hostname (not mixing `127.0.0.1` and `localhost`) to preserve session cookies across OAuth redirects.

