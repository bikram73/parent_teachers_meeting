# Node.js Version - Parent-Teacher Meeting Management System

This is a **Node.js + Express** version of the PHP project, optimized for **Vercel deployment**.

## Tech Stack

- **Runtime**: Node.js 18+
- **Framework**: Express.js
- **Database**: PostgreSQL (Supabase compatible)
- **Session**: express-session with PostgreSQL store
- **Authentication**: bcryptjs for password hashing
- **Deployment**: Vercel

## Quick Start

### 1. Install Dependencies

```bash
npm install
```

### 2. Setup Environment Variables

Create a `.env` file in the root directory:

```bash
cp .env.example .env
```

Then edit `.env` with your actual values:

```
DATABASE_URL=postgresql://user:password@localhost:5432/meeting_db
SESSION_SECRET=your-secret-key-here
NODE_ENV=development
PORT=3000
```

### 3. Database Setup

Run the database schema from `database.sql` in your PostgreSQL or Supabase database.

### 4. Run Locally

```bash
npm run dev
```

The server will run on `http://localhost:3000`

## Deployment to Vercel

### Step 1: Push to GitHub

```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/your-repo.git
git push -u origin main
```

### Step 2: Deploy on Vercel

1. Go to [vercel.com](https://vercel.com)
2. Click "New Project"
3. Import your GitHub repository
4. Set Environment Variables:
   - `DATABASE_URL` - Your Supabase/PostgreSQL connection string
   - `SESSION_SECRET` - A random secret key
5. Click "Deploy"

### Step 3: Update Database Connection

If using **Supabase**, your `DATABASE_URL` should look like:
```
postgresql://postgres:[PASSWORD]@[PROJECT_REF].supabase.co:5432/postgres
```

## Project Structure

```
├── server.js                 # Main server file
├── config/
│   └── database.js          # Database configuration
├── routes/
│   ├── auth.js              # Authentication routes (login, register, logout)
│   ├── dashboard.js         # Dashboard routes
│   └── meetings.js          # Meetings CRUD routes
├── public/                  # Static files (CSS, JS, images)
├── views/                   # EJS template files
├── package.json             # Node dependencies
├── vercel.json              # Vercel configuration
└── database.sql             # Database schema
```

## API Endpoints

### Authentication
- `POST /auth/register` - Register new user
- `POST /auth/login` - Login user
- `GET /auth/logout` - Logout user

### Meetings
- `GET /meetings/parent` - Get parent's meetings
- `GET /meetings/teacher` - Get teacher's meetings
- `POST /meetings/create` - Create new meeting
- `PUT /meetings/update/:id` - Update meeting
- `DELETE /meetings/delete/:id` - Delete meeting
- `PUT /meetings/accept/:id` - Accept meeting
- `PUT /meetings/reject/:id` - Reject meeting

### Dashboard
- `GET /dashboard/parent` - Parent dashboard
- `GET /dashboard/teacher` - Teacher dashboard

## Conversion from PHP

Key differences between PHP and Node.js versions:

| PHP | Node.js |
|-----|---------|
| `$_SESSION` | `req.session` |
| `$_POST` / `$_GET` | `req.body` / `req.query` |
| `mysqli_query()` | `pool.query()` |
| `echo json_encode()` | `res.json()` |
| `include/require` | `require()` |
| Global error handling | Express middleware |

## Security Features

✅ Password hashing with bcryptjs
✅ SQL injection prevention with parameterized queries
✅ Session security with HTTPOnly cookies
✅ CORS enabled
✅ Environment variables for sensitive data

## Requirements

- Node.js 18 or higher
- PostgreSQL database or Supabase account
- Vercel account (for deployment)

## Troubleshooting

### Database connection errors
- Check `DATABASE_URL` format
- Ensure database is running
- Verify credentials

### Session not persisting
- Ensure `SESSION_SECRET` is set
- Check PostgreSQL session table exists
- Verify cookies are enabled in browser

### Vercel deployment issues
- Check environment variables are set correctly
- View Vercel logs: `vercel logs`
- Ensure package.json has correct main entry point

## Next Steps

1. ✅ Convert static pages to EJS templates (in `views/` folder)
2. ✅ Add frontend with React/Vue (create `frontend/` folder)
3. ✅ Implement email notifications
4. ✅ Add calendar integration
5. ✅ Implement file uploads for meeting notes

## Support

For issues or questions, refer to:
- [Express.js Documentation](https://expressjs.com/)
- [Vercel Documentation](https://vercel.com/docs)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
