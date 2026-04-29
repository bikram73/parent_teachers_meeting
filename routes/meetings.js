const express = require('express');
const pool = require('../config/database');
const router = express.Router();

// Middleware to check authentication
const requireAuth = (req, res, next) => {
  if (!req.session.userId) {
    return res.status(401).json({ error: 'Unauthorized' });
  }
  next();
};

// Get all meetings for parent
router.get('/parent', requireAuth, async (req, res) => {
  try {
    if (req.session.userType !== 'parent') {
      return res.status(403).json({ error: 'Forbidden' });
    }

    const result = await pool.query(
      'SELECT * FROM meetings WHERE parent_id = $1 ORDER BY meeting_date DESC',
      [req.session.userId]
    );

    res.json(result.rows);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Get all meetings for teacher
router.get('/teacher', requireAuth, async (req, res) => {
  try {
    if (req.session.userType !== 'teacher') {
      return res.status(403).json({ error: 'Forbidden' });
    }

    const result = await pool.query(
      'SELECT * FROM meetings WHERE teacher_id = $1 ORDER BY meeting_date DESC',
      [req.session.userId]
    );

    res.json(result.rows);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Create new meeting
router.post('/create', requireAuth, async (req, res) => {
  try {
    const { parentName, studentName, subject, teacherName, meetingDate, meetingTime } = req.body;

    const result = await pool.query(
      'INSERT INTO meetings (parent_name, student_name, subject, teacher_name, meeting_date, meeting_time, status) VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING *',
      [parentName, studentName, subject, teacherName, meetingDate, meetingTime, 'scheduled']
    );

    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Update meeting
router.put('/update/:id', requireAuth, async (req, res) => {
  try {
    const { id } = req.params;
    const { meetingDate, meetingTime } = req.body;

    const result = await pool.query(
      'UPDATE meetings SET meeting_date = $1, meeting_time = $2 WHERE id = $3 RETURNING *',
      [meetingDate, meetingTime, id]
    );

    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Delete meeting
router.delete('/delete/:id', requireAuth, async (req, res) => {
  try {
    const { id } = req.params;

    await pool.query('DELETE FROM meetings WHERE id = $1', [id]);

    res.json({ success: true, message: 'Meeting deleted' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Accept meeting
router.put('/accept/:id', requireAuth, async (req, res) => {
  try {
    const { id } = req.params;

    const result = await pool.query(
      'UPDATE meetings SET response_status = $1 WHERE id = $2 RETURNING *',
      ['accepted', id]
    );

    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Reject meeting
router.put('/reject/:id', requireAuth, async (req, res) => {
  try {
    const { id, reason } = req.body;

    const result = await pool.query(
      'UPDATE meetings SET response_status = $1, rejection_reason = $2 WHERE id = $3 RETURNING *',
      ['rejected', reason, id]
    );

    res.json({ success: true, data: result.rows[0] });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
