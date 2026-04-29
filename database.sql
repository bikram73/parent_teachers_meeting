-- Supabase / PostgreSQL schema

CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TABLE IF NOT EXISTS parent_users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS teacher_users (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    subject VARCHAR(100) NOT NULL,
    signup_code VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS teacher_invite_codes (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(100) UNIQUE NOT NULL,
    is_used BOOLEAN NOT NULL DEFAULT FALSE,
    teacher_user_id BIGINT UNIQUE,
    used_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT fk_teacher_invite_teacher
        FOREIGN KEY (teacher_user_id)
        REFERENCES teacher_users(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS meetings (
    id BIGSERIAL PRIMARY KEY,
    parent_name VARCHAR(255) NOT NULL,
    student_name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    teacher_name VARCHAR(255) NOT NULL,
    meeting_date DATE NOT NULL,
    meeting_time TIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'scheduled',
    response_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    rejection_reason TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT meetings_status_check CHECK (status IN ('scheduled', 'completed', 'cancelled')),
    CONSTRAINT meetings_response_check CHECK (response_status IN ('pending', 'accepted', 'rejected'))
);

CREATE TABLE IF NOT EXISTS student_performance (
    id BIGSERIAL PRIMARY KEY,
    usn VARCHAR(20) NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    parent_name VARCHAR(100) NOT NULL,
    marks NUMERIC(5,2) NOT NULL,
    subject VARCHAR(50) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS notifications (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trigger_student_performance_updated_at ON student_performance;
CREATE TRIGGER trigger_student_performance_updated_at
BEFORE UPDATE ON student_performance
FOR EACH ROW
EXECUTE FUNCTION set_updated_at();
