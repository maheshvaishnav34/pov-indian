const express = require('express');
const router = express.Router();
const rentalController = require('../controllers/rentalController');

// 1. Curated picks
router.get('/curated', rentalController.getCurated);

// 2. Visits Management (PRD Section 16)
router.get('/visits', rentalController.listVisits);
router.post('/visits', rentalController.createVisit);
router.get('/visits/:token', rentalController.getVisitByToken);
router.patch('/visits/:token', rentalController.updateVisitStatus);

// 3. 'I Need a Property' Demand-Side Reverse Marketplace (PRD Section 17)
router.post('/requirements', rentalController.postRequirement);
router.get('/requirements/:id/matches', rentalController.getRequirementMatches);

// 4. Assisted WhatsApp Onboarding Intake (PRD Section 5 & 11)
router.post('/assisted-onboarding', rentalController.submitAssistedOnboarding);

// 5. Self-Serve Property Listing Creation & Management (PRD Section 11)
router.post('/listings', rentalController.createProperty);
router.put('/listings/:id', rentalController.updateProperty);
router.delete('/listings/:id', rentalController.deleteProperty);

// 6. Property Search & Catalog (PRD Section 10)
router.get('/', rentalController.getProperties);

// 7. Single Property Detail (PRD Section 12)
router.get('/:id', rentalController.getPropertyById);

module.exports = router;
