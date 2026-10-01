const express = require('express');
const router = express.Router();
const listingController = require('../controllers/listingController');
const { adminOrInternalGuard } = require('../middlewares/authMiddleware');

router.get('/', listingController.getListings);
router.get('/:id', listingController.getListingById);

// Admin / Internal routes
router.post('/', adminOrInternalGuard, listingController.createListing);
router.put('/:id', adminOrInternalGuard, listingController.updateListing);
router.delete('/:id', adminOrInternalGuard, listingController.deleteListing);

module.exports = router;

