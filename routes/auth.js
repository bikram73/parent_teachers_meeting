const express = require('express');
const bcrypt = require('bcryptjs');
const pool = require('../config/database');
const router = express.Router();

// Register route
router.post('/register', async (req, res) => {
  try {
    const { name, email, password, userType, phone, subject, signupCode } = req.body;

    // Validate input
    if (!name || !email || !password || !userType) {
      return res.status(400).json({ error: 'Missing required fields' });
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(password, 10);

    if (userType === 'parent') {
      const result = await pool.query(
        'INSERT INTO parent_users (name, email, password, phone) VALUES ($1, $2, $3, $4) RETURNING id',
        [name, email, hashedPassword, phone]
      );

      req.session.userId = result.rows[0].id;
      req.session.userType = 'parent';
      res.json({ success: true, message: 'Parent registered successfully' });

    } else if (userType === 'teacher') {
      // Verify signup code
      const codeCheck = await pool.query(
        'SELECT * FROM teacher_invite_codes WHERE code = $1 AND is_used = false',
        [signupCode]
      );

      if (codeCheck.rows.length === 0) {
        return res.status(400).json({ error: 'Invalid or used signup code' });
      }

      const result = await pool.query(
        'INSERT INTO teacher_users (name, email, password, phone, subject, signup_code) VALUES ($1, $2, $3, $4, $5, $6) RETURNING id',
        [name, email, hashedPassword, phone, subject, signupCode]
      );

      // Mark code as used
      await pool.query(
        'UPDATE teacher_invite_codes SET is_used = true, teacher_user_id = $1, used_at = NOW() WHERE code = $2',
        [result.rows[0].id, signupCode]
      );

      req.session.userId = result.rows[0].id;
      req.session.userType = 'teacher';
      res.json({ success: true, message: 'Teacher registered successfully' });
    }
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Login route
router.post('/login', async (req, res) => {
  try {
    const { email, password, userType } = req.body;

    if (!email || !password || !userType) {
      return res.status(400).json({ error: 'Missing email, password, or user type' });
    }

    let tableName = userType === 'parent' ? 'parent_users' : 'teacher_users';
    const result = await pool.query(`SELECT * FROM ${tableName} WHERE email = $1`, [email]);

    if (result.rows.length === 0) {
      return res.status(401).json({ error: 'Invalid email or password' });
    }

    const user = result.rows[0];
    const passwordMatch = await bcrypt.compare(password, user.password);

    if (!passwordMatch) {
      return res.status(401).json({ error: 'Invalid email or password' });
    }

    req.session.userId = user.id;
    req.session.userType = userType;
    req.session.userName = user.name;

    res.json({ success: true, message: 'Login successful', userType });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Logout route
router.get('/logout', (req, res) => {
  req.session.destroy((err) => {
    if (err) return res.status(500).json({ error: 'Logout failed' });
    res.json({ success: true, message: 'Logged out successfully' });
  });
});

module.exports = router;
