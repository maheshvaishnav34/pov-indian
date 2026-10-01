const express = require('express');
const router = express.Router();
const locationController = require('../controllers/locationController');

router.get('/', locationController.getAllLocations);
router.get('/:slug', locationController.getLocationBySlug);
router.post('/', locationController.createLocation);

module.exports = router;

