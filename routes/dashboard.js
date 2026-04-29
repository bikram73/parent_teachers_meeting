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

// Parent dashboard
router.get('/parent', requireAuth, async (req, res) => {
  try {
    if (req.session.userType !== 'parent') {
      return res.status(403).json({ error: 'Forbidden' });
    }

    // Get parent info
    const parentResult = await pool.query(
      'SELECT * FROM parent_users WHERE id = $1',
      [req.session.userId]
    );

    // Get upcoming meetings
    const meetingsResult = await pool.query(
      'SELECT * FROM meetings WHERE parent_name = $1 AND meeting_date >= CURRENT_DATE ORDER BY meeting_date ASC LIMIT 5',
      [parentResult.rows[0].name]
    );

    res.json({
      user: parentResult.rows[0],
      upcomingMeetings: meetingsResult.rows,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

// Teacher dashboard
router.get('/teacher', requireAuth, async (req, res) => {
  try {
    if (req.session.userType !== 'teacher') {
      return res.status(403).json({ error: 'Forbidden' });
    }

    // Get teacher info
    const teacherResult = await pool.query(
      'SELECT * FROM teacher_users WHERE id = $1',
      [req.session.userId]
    );

    // Get pending meetings
    const meetingsResult = await pool.query(
      'SELECT * FROM meetings WHERE teacher_name = $1 AND response_status = $2 ORDER BY meeting_date ASC',
      [teacherResult.rows[0].name, 'pending']
    );

    // Get scheduled meetings
    const scheduledResult = await pool.query(
      'SELECT * FROM meetings WHERE teacher_name = $1 AND status = $2 ORDER BY meeting_date ASC',
      [teacherResult.rows[0].name, 'scheduled']
    );

    res.json({
      user: teacherResult.rows[0],
      pendingMeetings: meetingsResult.rows,
      scheduledMeetings: scheduledResult.rows,
    });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;
