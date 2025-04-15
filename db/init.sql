CREATE TABLE IF NOT EXISTS tasks (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Seed data
INSERT INTO tasks (title, description, completed, created_at) 
VALUES 
    ('Buy groceries', 'Milk, Bread, Eggs', false, '2025-03-24T14:00:00Z'),
    ('Finish project', 'Complete the PHP + PostgreSQL test', false, '2025-03-24T15:00:00Z');
