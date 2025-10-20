# Frontend - React + TypeScript

This is the frontend application for the Authentication System, built with React 19 and TypeScript.

> **Note**: For complete project setup and installation instructions, see the main [README.md](../README.md) in the project root.

## Frontend Setup

1. **Install dependencies:**
   ```bash
   npm install
   ```

2. **Environment Configuration:**
   Create a `.env` file in the frontend directory:
   ```env
   VITE_API_URL=http://localhost:8000
   VITE_APP_NAME="Authentication System"
   ```

## Running the Frontend

**Development Server:**
```bash
npm run dev
```

**Build for Production:**
```bash
npm run build
```

**Preview Production Build:**
```bash
npm run preview
```

## Testing

```bash
# Run tests
npm run test

# Run tests with UI
npm run test:ui

# Run tests with coverage
npm run test:coverage
```

## Frontend Technology Stack

- **React 19** - UI Framework
- **TypeScript** - Type Safety
- **Vite** - Build Tool
- **Bootstrap 5** - CSS Framework
- **React Router** - Client-side Routing
- **Axios** - HTTP Client
- **React Hook Form** - Form Management
- **Zod** - Schema Validation
- **Vitest** - Testing Framework

## Project Structure

```
frontend/
├── src/
│   ├── components/    # Reusable components
│   ├── pages/        # Page components
│   ├── contexts/     # React contexts
│   ├── api.js        # API configuration
│   └── ...
├── public/           # Static assets
├── tests/           # Test files
└── package.json     # Dependencies and scripts
```
