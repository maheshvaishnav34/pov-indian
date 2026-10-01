const express = require('express');
const router = express.Router();
const educationController = require('../controllers/educationController');

// 1. Public Discovery Routes (Layer 2)
router.get('/institutions', educationController.getInstitutions);
router.get('/institutions/:id', educationController.getInstitutionById);
router.get('/updates', educationController.getUpdates);

// Luxury Ecosystem Routes
router.get('/exams', educationController.getExams);
router.get('/careers', educationController.getCareers);
router.get('/scholarships', educationController.getScholarships);
router.get('/reviews', educationController.getReviews);
router.get('/applications', educationController.getApplications);
router.post('/assessment', educationController.submitAssessment);
router.get('/search', educationController.universalSearch);

// 2. Student Inquiries & Admission Leads (Layer 3)
router.post('/leads', educationController.createLead);

// 3. Admin & Analytics Routes (Layer 4)
router.get('/stats', educationController.getStats);
router.get('/admin/leads', educationController.listLeads);
router.patch('/admin/leads/:id/stage', educationController.updateLeadStage);

// 4. Admin Management: Colleges / Offerings / Exams / Scholarships
router.post('/institutions', educationController.createInstitution);
router.put('/institutions/:id', educationController.updateInstitution);
router.delete('/institutions/:id', educationController.deleteInstitution);
router.post('/institutions/:id/offerings', educationController.createOffering);
router.delete('/offerings/:id', educationController.deleteOffering);
router.post('/exams', educationController.createExam);
router.delete('/exams/:id', educationController.deleteExam);
router.post('/scholarships', educationController.createScholarship);
router.delete('/scholarships/:id', educationController.deleteScholarship);

module.exports = router;
