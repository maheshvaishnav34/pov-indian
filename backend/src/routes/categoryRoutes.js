const express = require('express');
const router = express.Router();
const categoryController = require('../controllers/categoryController');

router.get('/', categoryController.getAllCategories);
router.get('/:slug', categoryController.getCategoryBySlug);
router.post('/', categoryController.createCategory);
router.post('/:id/toggle-signup', categoryController.toggleCategorySignup);
router.patch('/:id/toggle-signup', categoryController.toggleCategorySignup);

module.exports = router;

