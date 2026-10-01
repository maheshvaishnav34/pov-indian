<?php
/**
 * Admin Modals Component (Add Listing, Edit Listing, Add/Edit Rentals, Add Category, Add Location, Add Blog)
 */
?>
<!-- ================= Modal 1: Add Directory Listing ================= -->
<div class="modal-backdrop" id="addListingModal" onclick="handleBackdropClick(event, 'addListingModal')">
  <div class="modal-window">
    <div class="modal-header">
      <h3 class="modal-title">
        <i class="fa-solid fa-plus-circle" style="color:var(--primary);"></i> Add Directory Listing
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addListingModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_listing" />
      <div class="modal-body">
        
        <!-- Switch banner to Rentals & Stays -->
        <div style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; margin-bottom:16px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
          <div style="font-size:12.5px; color:#334155;">
            <strong>Want to add a PG, Flat, or Homestay?</strong> Add to <em>Find a place that fits your life.</em>
          </div>
          <button type="button" class="btn-topbar btn-topbar-primary" style="padding:6px 12px; font-size:12px; white-space:nowrap;" onclick="closeModal('addListingModal'); openModal('addRentalModal');">
            <i class="fa-solid fa-house-chimney"></i> Add Rental Property
          </button>
        </div>

        <div class="form-group">
          <label class="form-label">Business Title *</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. Taj Mahal Heritage Retreat" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Category *</label>
            <select name="category_id" class="form-control" required>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)($cat['id'] ?? 1) ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Location (City)</label>
            <select name="location_id" class="form-control">
              <option value="">-- Select Destination --</option>
              <?php foreach ($locations as $loc): ?>
                <option value="<?= (int)($loc['id'] ?? 1) ?>"><?= htmlspecialchars($loc['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Location Address Text</label>
            <input type="text" name="location_text" class="form-control" placeholder="e.g. Connaught Place, New Delhi" />
          </div>
          <div class="form-group">
            <label class="form-label">Contact Phone</label>
            <input type="text" name="phone" class="form-control" placeholder="e.g. +91 98765 43210" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Price / Price Range</label>
            <input type="text" name="price" class="form-control" placeholder="e.g. ₹2,500 - ₹5,000" />
          </div>
          <div class="form-group">
            <label class="form-label">Rating (1.0 to 5.0)</label>
            <input type="number" step="0.1" min="1.0" max="5.0" name="rating" class="form-control" value="4.9" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Featured Badge</label>
            <select name="badge" class="form-control">
              <option value="">None</option>
              <option value="Popular">Popular</option>
              <option value="Top Rated">Top Rated</option>
              <option value="Featured">Featured</option>
              <option value="Verified">Verified</option>
              <option value="New">New</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">
              <i class="fa-regular fa-image" style="color:var(--primary); margin-right:4px;"></i> Cover Image URL
            </label>
            <input type="text" name="image" id="addListingImage" class="form-control" placeholder="Paste image URL (https://...) or preset" value="realestate_03.webp" oninput="povUpdateListingPreview()" />
            <select class="form-control" id="addListingImagePreset" style="margin-top:6px; font-size:12.5px; color:#475569;" onchange="if(this.value){ document.getElementById('addListingImage').value = this.value; povUpdateListingPreview(); }">
              <option value="">-- Or pick from preset gallery --</option>
              <option value="realestate_03.webp">Heritage Haveli Stay (realestate_03.webp)</option>
              <option value="realestate_01.webp">Modern Apartment (realestate_01.webp)</option>
              <option value="restaurant_05.webp">Coastal Seafood Kitchen (restaurant_05.webp)</option>
              <option value="cafe_01.webp">Filter Coffee & Bites (cafe_01.webp)</option>
              <option value="tourism_01.jpg">Kerala Backwaters (tourism_01.jpg)</option>
              <option value="tourism_02.jpg">Golden Triangle Tour (tourism_02.jpg)</option>
              <option value="wellness_01.jpg">Ayurveda Wellness Retreat (wellness_01.jpg)</option>
              <option value="wellness_02.jpg">Yoga & Panchakarma (wellness_02.jpg)</option>
              <option value="hotel_01.jpg">Luxury Heritage Resort (hotel_01.jpg)</option>
              <option value="hotel_02.jpg">Boutique Homestay (hotel_02.jpg)</option>
              <option value="healthcare_01.jpg">Multispecialty Care (healthcare_01.jpg)</option>
              <option value="manufacturing_01.jpg">Precision Unit (manufacturing_01.jpg)</option>
              <option value="legal_01.jpg">Corporate Counsel (legal_01.jpg)</option>
              <option value="garden_01.jpg">Urban Garden Studio (garden_01.jpg)</option>
              <option value="automotive_03.webp">Express Detailing (automotive_03.webp)</option>
              <option value="beauty_01.jpg">Heritage Beauty Lounge (beauty_01.jpg)</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group" style="grid-column: 1 / -1;">
            <label class="form-label">
              <i class="fa-solid fa-certificate" style="color:var(--primary); margin-right:4px;"></i> Business Logo / Brand Icon URL
            </label>
            <div style="display:flex; gap:10px;">
              <input type="text" name="avatar" id="addListingAvatar" class="form-control" style="flex:1;" placeholder="Paste logo URL (https://...) or choose avatar preset" value="ryan.webp" oninput="povUpdateListingPreview()" />
              <select class="form-control" id="addListingAvatarPreset" style="width:230px; font-size:12.5px; color:#475569;" onchange="if(this.value){ document.getElementById('addListingAvatar').value = this.value; povUpdateListingPreview(); }">
                <option value="">-- Choose preset logo --</option>
                <option value="ryan.webp">Ryan Avatar (ryan.webp)</option>
                <option value="james.webp">James Avatar (james.webp)</option>
                <option value="emma.webp">Emma Avatar (emma.webp)</option>
                <option value="david.webp">David Avatar (david.webp)</option>
                <option value="lisa.webp">Lisa Avatar (lisa.webp)</option>
                <option value="t1.webp">Brand Icon 1 (t1.webp)</option>
                <option value="t2.webp">Brand Icon 2 (t2.webp)</option>
                <option value="t3.webp">Brand Icon 3 (t3.webp)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Live Visual Preview Box -->
        <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:16px;">
          <div style="position:relative; width:94px; height:62px; border-radius:8px; overflow:hidden; background:#e2e8f0; flex-shrink:0; box-shadow:0 2px 6px rgba(0,0,0,0.08);">
            <img id="previewCoverImg" src="<?= htmlspecialchars(pov_url('assets/img/listings/realestate_03.webp')) ?>" alt="Cover Preview" style="width:100%; height:100%; object-fit:cover;" onerror="this.src='<?= htmlspecialchars(pov_url('assets/img/listings/realestate_01.webp')) ?>'" />
            <div style="position:absolute; bottom:4px; right:4px; width:26px; height:26px; border-radius:50%; border:2px solid #ffffff; overflow:hidden; background:#ffffff; box-shadow:0 1px 4px rgba(0,0,0,0.2);">
              <img id="previewLogoImg" src="<?= htmlspecialchars(pov_url('assets/img/avatars/ryan.webp')) ?>" alt="Logo Preview" style="width:100%; height:100%; object-fit:cover;" onerror="this.src='<?= htmlspecialchars(pov_url('assets/img/avatars/ryan.webp')) ?>'" />
            </div>
          </div>
          <div style="flex:1; min-width:0;">
            <div style="font-size:12px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
              <i class="fa-solid fa-eye" style="color:var(--primary); margin-right:4px;"></i> Live Image & Logo Preview
            </div>
            <div style="font-size:12px; color:#64748b; line-height:1.4;">Paste any external image or logo URL (e.g. Unsplash, Cloudinary, AWS S3) or pick local presets. Live preview updates as you type.</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Business Description</label>
          <textarea name="description" id="addListingDesc" class="form-control" rows="3" placeholder="Provide a concise description of the services, ambiance, or uniqueness..." oninput="povUpdateSeoLive('add')"></textarea>
        </div>

        <!-- ================= SEO & Metadata Suite (Add Modal) ================= -->
        <div style="margin-top:16px; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#ffffff;">
          <div style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:11px 15px; display:flex; align-items:center; justify-content:space-between; cursor:pointer;" onclick="povToggleSeoPanel('addListingSeoBody', this)">
            <div style="display:flex; align-items:center; gap:9px;">
              <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:6px; background:#eff6ff; color:#2563eb; font-size:12px;">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
              </span>
              <div>
                <strong style="font-size:13px; color:#1e293b;">Search Engine Optimization (SEO & Meta Tags)</strong>
                <span style="font-size:11px; color:#64748b; margin-left:6px;">Google Search Title, Description & Schema</span>
              </div>
            </div>
            <span class="seo-toggle-icon" style="font-size:12px; color:#64748b; transition:transform 0.2s;"><i class="fa-solid fa-chevron-down"></i></span>
          </div>

          <div id="addListingSeoBody" style="display:none; padding:15px; background:#fafafa;">
            <!-- Live Google SERP Preview Box -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
              <div style="font-size:10.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; display:flex; align-items:center; justify-content:space-between;">
                <span><i class="fa-brands fa-google" style="color:#ea4335; margin-right:4px;"></i> Google Search Snippet Preview</span>
                <span style="color:#10b981; font-weight:600;"><i class="fa-solid fa-bolt"></i> Live</span>
              </div>
              <div style="font-family:arial,sans-serif;">
                <div style="font-size:12px; color:#202124; margin-bottom:2px; display:flex; align-items:center; gap:5px;">
                  <span style="display:inline-block; width:15px; height:15px; border-radius:50%; background:#e2e8f0; text-align:center; font-size:9px; line-height:15px;">🇮🇳</span>
                  <span>https://povindian.com › listings › <span id="addPreviewSlug" style="color:#5f6368;">listing-slug</span></span>
                </div>
                <div id="addPreviewTitle" style="font-size:17px; color:#1a0dab; line-height:1.3; font-weight:400; margin-bottom:3px; cursor:pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                  Directory Listing Title | POV Indian Verified Directory
                </div>
                <div id="addPreviewDesc" style="font-size:12.5px; color:#4d5156; line-height:1.4;">
                  Enter a meta description to preview how this directory listing appears in Google search engine results...
                </div>
              </div>
            </div>

            <!-- SEO Title -->
            <div class="form-group">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                <label class="form-label" style="margin:0;">SEO Meta Title</label>
                <span id="addMetaTitleCounter" style="font-size:11px; color:#64748b;">0 / 60 chars</span>
              </div>
              <input type="text" name="meta_title" id="addMetaTitle" class="form-control" placeholder="e.g. Best Heritage Stay in Jaipur | POV Indian" oninput="povUpdateSeoLive('add')" />
              <div style="font-size:11px; color:#94a3b8; margin-top:3px;">Recommended 50–60 characters. Defaults to listing title + brand if blank.</div>
            </div>

            <!-- Slug & Schema Type -->
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">SEO URL Slug</label>
                <input type="text" name="slug" id="addSlug" class="form-control" placeholder="e.g. heritage-haveli-stay-jaipur" oninput="povUpdateSeoLive('add')" />
              </div>
              <div class="form-group">
                <label class="form-label">Schema.org Rich Snippet Type</label>
                <select name="schema_type" class="form-control">
                  <option value="LocalBusiness" selected>LocalBusiness (Default)</option>
                  <option value="Hotel">Hotel / Accommodation</option>
                  <option value="Restaurant">Restaurant / Café</option>
                  <option value="MedicalClinic">Medical Clinic / Healthcare</option>
                  <option value="EducationalOrganization">College / University</option>
                  <option value="TravelAgency">Tourism & Travel Agency</option>
                  <option value="RealEstateAgent">Real Estate Agency</option>
                  <option value="Store">Store / Retail Business</option>
                  <option value="HealthAndBeautyBusiness">Spa & Wellness Center</option>
                </select>
              </div>
            </div>

            <!-- Meta Description -->
            <div class="form-group">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                <label class="form-label" style="margin:0;">Meta Description</label>
                <span id="addMetaDescCounter" style="font-size:11px; color:#64748b;">0 / 160 chars</span>
              </div>
              <textarea name="meta_description" id="addMetaDesc" class="form-control" rows="2" placeholder="Brief summary of business for Google search snippet (140-160 chars recommended)..." oninput="povUpdateSeoLive('add')"></textarea>
            </div>

            <!-- Meta Keywords & Canonical -->
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Focus Keywords / Meta Tags</label>
                <input type="text" name="meta_keywords" class="form-control" placeholder="e.g. jaipur haveli, heritage hotel, verified stays india" />
              </div>
              <div class="form-group">
                <label class="form-label">Custom Canonical URL (Optional)</label>
                <input type="text" name="canonical_url" class="form-control" placeholder="https://povindian.com/listing-detail.php?id=..." />
              </div>
            </div>

            <!-- Open Graph Image -->
            <div class="form-group">
              <label class="form-label"><i class="fa-solid fa-share-nodes" style="color:var(--primary); margin-right:4px;"></i> Open Graph (OG) Social Image URL</label>
              <input type="text" name="og_image" class="form-control" placeholder="Image URL for WhatsApp, Twitter & Facebook cards (defaults to cover photo)" />
            </div>

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addListingModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary"><i class="fa-solid fa-plus-circle"></i> Save Listing</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= Modal 2: Edit Directory Listing ================= -->
<div class="modal-backdrop" id="editListingModal" onclick="handleBackdropClick(event, 'editListingModal')">
  <div class="modal-window">
    <div class="modal-header">
      <h3 class="modal-title">
        <i class="fa-solid fa-pen-to-square" style="color:var(--primary);"></i> Edit Listing #<span id="editListingIdDisplay"></span>
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('editListingModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="edit_listing" />
      <input type="hidden" name="id" id="editListingId" />
      <div class="modal-body">
        
        <div class="form-group">
          <label class="form-label">Business Title *</label>
          <input type="text" name="title" id="editListingTitle" class="form-control" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Category *</label>
            <select name="category_id" id="editListingCatId" class="form-control" required>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)($cat['id'] ?? 1) ?>" data-name="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Location (City)</label>
            <select name="location_id" id="editListingLocId" class="form-control">
              <option value="">-- Select Destination --</option>
              <?php foreach ($locations as $loc): ?>
                <option value="<?= (int)($loc['id'] ?? 1) ?>"><?= htmlspecialchars($loc['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Location Address Text</label>
            <input type="text" name="location_text" id="editListingLocText" class="form-control" placeholder="e.g. Connaught Place, New Delhi" />
          </div>
          <div class="form-group">
            <label class="form-label">Contact Phone</label>
            <input type="text" name="phone" id="editListingPhone" class="form-control" placeholder="e.g. +91 98765 43210" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Price / Price Range</label>
            <input type="text" name="price" id="editListingPrice" class="form-control" placeholder="e.g. ₹2,500 - ₹5,000" />
          </div>
          <div class="form-group">
            <label class="form-label">Rating (1.0 to 5.0)</label>
            <input type="number" step="0.1" min="1.0" max="5.0" name="rating" id="editListingRating" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Featured Badge</label>
            <select name="badge" id="editListingBadge" class="form-control">
              <option value="">None</option>
              <option value="Popular">Popular</option>
              <option value="Top Rated">Top Rated</option>
              <option value="Featured">Featured</option>
              <option value="Verified">Verified</option>
              <option value="New">New</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">
              <i class="fa-regular fa-image" style="color:var(--primary); margin-right:4px;"></i> Cover Image URL
            </label>
            <input type="text" name="image" id="editListingImage" class="form-control" oninput="povUpdateEditListingPreview()" />
            <select class="form-control" id="editListingImagePreset" style="margin-top:6px; font-size:12.5px; color:#475569;" onchange="if(this.value){ document.getElementById('editListingImage').value = this.value; povUpdateEditListingPreview(); }">
              <option value="">-- Pick from preset gallery --</option>
              <option value="realestate_03.webp">Heritage Haveli Stay (realestate_03.webp)</option>
              <option value="realestate_01.webp">Modern Apartment (realestate_01.webp)</option>
              <option value="restaurant_05.webp">Coastal Seafood Kitchen (restaurant_05.webp)</option>
              <option value="cafe_01.webp">Filter Coffee & Bites (cafe_01.webp)</option>
              <option value="tourism_01.jpg">Kerala Backwaters (tourism_01.jpg)</option>
              <option value="tourism_02.jpg">Golden Triangle Tour (tourism_02.jpg)</option>
              <option value="wellness_01.jpg">Ayurveda Wellness Retreat (wellness_01.jpg)</option>
              <option value="wellness_02.jpg">Yoga & Panchakarma (wellness_02.jpg)</option>
              <option value="hotel_01.jpg">Luxury Heritage Resort (hotel_01.jpg)</option>
              <option value="hotel_02.jpg">Boutique Homestay (hotel_02.jpg)</option>
              <option value="healthcare_01.jpg">Multispecialty Care (healthcare_01.jpg)</option>
              <option value="manufacturing_01.jpg">Precision Unit (manufacturing_01.jpg)</option>
              <option value="legal_01.jpg">Corporate Counsel (legal_01.jpg)</option>
              <option value="garden_01.jpg">Urban Garden Studio (garden_01.jpg)</option>
              <option value="automotive_03.webp">Express Detailing (automotive_03.webp)</option>
              <option value="beauty_01.jpg">Heritage Beauty Lounge (beauty_01.jpg)</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group" style="grid-column: 1 / -1;">
            <label class="form-label">
              <i class="fa-solid fa-certificate" style="color:var(--primary); margin-right:4px;"></i> Business Logo / Avatar URL
            </label>
            <div style="display:flex; gap:10px;">
              <input type="text" name="avatar" id="editListingAvatar" class="form-control" style="flex:1;" oninput="povUpdateEditListingPreview()" />
              <select class="form-control" id="editListingAvatarPreset" style="width:230px; font-size:12.5px; color:#475569;" onchange="if(this.value){ document.getElementById('editListingAvatar').value = this.value; povUpdateEditListingPreview(); }">
                <option value="">-- Choose preset logo --</option>
                <option value="ryan.webp">Ryan Avatar (ryan.webp)</option>
                <option value="james.webp">James Avatar (james.webp)</option>
                <option value="emma.webp">Emma Avatar (emma.webp)</option>
                <option value="david.webp">David Avatar (david.webp)</option>
                <option value="lisa.webp">Lisa Avatar (lisa.webp)</option>
                <option value="t1.webp">Brand Icon 1 (t1.webp)</option>
                <option value="t2.webp">Brand Icon 2 (t2.webp)</option>
                <option value="t3.webp">Brand Icon 3 (t3.webp)</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Live Visual Preview Box -->
        <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:12px; padding:12px 16px; margin-bottom:18px; display:flex; align-items:center; gap:16px;">
          <div style="position:relative; width:94px; height:62px; border-radius:8px; overflow:hidden; background:#e2e8f0; flex-shrink:0; box-shadow:0 2px 6px rgba(0,0,0,0.08);">
            <img id="editPreviewCoverImg" src="" alt="Cover Preview" style="width:100%; height:100%; object-fit:cover;" onerror="this.src='<?= htmlspecialchars(pov_url('assets/img/listings/realestate_01.webp')) ?>'" />
            <div style="position:absolute; bottom:4px; right:4px; width:26px; height:26px; border-radius:50%; border:2px solid #ffffff; overflow:hidden; background:#ffffff; box-shadow:0 1px 4px rgba(0,0,0,0.2);">
              <img id="editPreviewLogoImg" src="" alt="Logo Preview" style="width:100%; height:100%; object-fit:cover;" onerror="this.src='<?= htmlspecialchars(pov_url('assets/img/avatars/ryan.webp')) ?>'" />
            </div>
          </div>
          <div style="flex:1; min-width:0;">
            <div style="font-size:12px; font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:2px;">
              <i class="fa-solid fa-eye" style="color:var(--primary); margin-right:4px;"></i> Live Image & Logo Preview
            </div>
            <div style="font-size:12px; color:#64748b; line-height:1.4;">Changes reflect in real time. Click Update Listing to commit changes.</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Business Description</label>
          <textarea name="description" id="editListingDesc" class="form-control" rows="3" oninput="povUpdateSeoLive('edit')"></textarea>
        </div>

        <!-- ================= SEO & Metadata Suite (Edit Modal) ================= -->
        <div style="margin-top:16px; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; background:#ffffff;">
          <div style="background:#f8fafc; border-bottom:1px solid #e2e8f0; padding:11px 15px; display:flex; align-items:center; justify-content:space-between; cursor:pointer;" onclick="povToggleSeoPanel('editListingSeoBody', this)">
            <div style="display:flex; align-items:center; gap:9px;">
              <span style="display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:6px; background:#f3e8ff; color:#9333ea; font-size:12px;">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
              </span>
              <div>
                <strong style="font-size:13px; color:#1e293b;">Search Engine Optimization (SEO & Meta Tags)</strong>
                <span style="font-size:11px; color:#64748b; margin-left:6px;">Custom Google Titles, Description, Slug & Schema</span>
              </div>
            </div>
            <span class="seo-toggle-icon" style="font-size:12px; color:#64748b; transition:transform 0.2s;"><i class="fa-solid fa-chevron-down"></i></span>
          </div>

          <div id="editListingSeoBody" style="display:none; padding:15px; background:#fafafa;">
            <!-- Live Google SERP Preview Box -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px; margin-bottom:16px; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
              <div style="font-size:10.5px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px; display:flex; align-items:center; justify-content:space-between;">
                <span><i class="fa-brands fa-google" style="color:#ea4335; margin-right:4px;"></i> Google Search Snippet Preview</span>
                <span style="color:#10b981; font-weight:600;"><i class="fa-solid fa-bolt"></i> Live</span>
              </div>
              <div style="font-family:arial,sans-serif;">
                <div style="font-size:12px; color:#202124; margin-bottom:2px; display:flex; align-items:center; gap:5px;">
                  <span style="display:inline-block; width:15px; height:15px; border-radius:50%; background:#e2e8f0; text-align:center; font-size:9px; line-height:15px;">🇮🇳</span>
                  <span>https://povindian.com › listings › <span id="editPreviewSlug" style="color:#5f6368;">listing-slug</span></span>
                </div>
                <div id="editPreviewTitle" style="font-size:17px; color:#1a0dab; line-height:1.3; font-weight:400; margin-bottom:3px; cursor:pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                  Directory Listing Title | POV Indian
                </div>
                <div id="editPreviewDesc" style="font-size:12.5px; color:#4d5156; line-height:1.4;">
                  Enter a meta description to preview how this directory listing appears in Google search engine results...
                </div>
              </div>
            </div>

            <!-- SEO Title -->
            <div class="form-group">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                <label class="form-label" style="margin:0;">SEO Meta Title</label>
                <span id="editMetaTitleCounter" style="font-size:11px; color:#64748b;">0 / 60 chars</span>
              </div>
              <input type="text" name="meta_title" id="editMetaTitle" class="form-control" placeholder="e.g. Best Heritage Stay in Jaipur | POV Indian" oninput="povUpdateSeoLive('edit')" />
              <div style="font-size:11px; color:#94a3b8; margin-top:3px;">Recommended 50–60 characters. Leave blank to inherit listing business title.</div>
            </div>

            <!-- Slug & Schema Type -->
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">SEO URL Slug</label>
                <input type="text" name="slug" id="editSlug" class="form-control" placeholder="e.g. heritage-haveli-stay-jaipur" oninput="povUpdateSeoLive('edit')" />
              </div>
              <div class="form-group">
                <label class="form-label">Schema.org Rich Snippet Type</label>
                <select name="schema_type" id="editSchemaType" class="form-control">
                  <option value="LocalBusiness">LocalBusiness (Default)</option>
                  <option value="Hotel">Hotel / Accommodation</option>
                  <option value="Restaurant">Restaurant / Café</option>
                  <option value="MedicalClinic">Medical Clinic / Healthcare</option>
                  <option value="EducationalOrganization">College / University</option>
                  <option value="TravelAgency">Tourism & Travel Agency</option>
                  <option value="RealEstateAgent">Real Estate Agency</option>
                  <option value="Store">Store / Retail Business</option>
                  <option value="HealthAndBeautyBusiness">Spa & Wellness Center</option>
                </select>
              </div>
            </div>

            <!-- Meta Description -->
            <div class="form-group">
              <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                <label class="form-label" style="margin:0;">Meta Description</label>
                <span id="editMetaDescCounter" style="font-size:11px; color:#64748b;">0 / 160 chars</span>
              </div>
              <textarea name="meta_description" id="editMetaDesc" class="form-control" rows="2" placeholder="Brief summary of business for Google search snippet (140-160 chars recommended)..." oninput="povUpdateSeoLive('edit')"></textarea>
            </div>

            <!-- Meta Keywords & Canonical -->
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Focus Keywords / Meta Tags</label>
                <input type="text" name="meta_keywords" id="editMetaKeywords" class="form-control" placeholder="e.g. jaipur haveli, heritage hotel, verified stays india" />
              </div>
              <div class="form-group">
                <label class="form-label">Custom Canonical URL (Optional)</label>
                <input type="text" name="canonical_url" id="editCanonicalUrl" class="form-control" placeholder="https://povindian.com/listing-detail.php?id=..." />
              </div>
            </div>

            <!-- Open Graph Image -->
            <div class="form-group">
              <label class="form-label"><i class="fa-solid fa-share-nodes" style="color:var(--primary); margin-right:4px;"></i> Open Graph (OG) Social Image URL</label>
              <input type="text" name="og_image" id="editOgImage" class="form-control" placeholder="Image URL for WhatsApp, Twitter & Facebook cards (defaults to cover photo)" />
            </div>

          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('editListingModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary"><i class="fa-solid fa-check"></i> Update Listing</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= Modal 3: Add Rental Property ("Find a place that fits your life.") ================= -->
<div class="modal-backdrop" id="addRentalModal" onclick="handleBackdropClick(event, 'addRentalModal')">
  <div class="modal-window" style="max-width:720px;">
    <div class="modal-header">
      <div>
        <h3 class="modal-title">
          <i class="fa-solid fa-house-chimney" style="color:var(--primary);"></i> Add Rental Property
        </h3>
        <div style="font-size:12px; color:#64748b; margin-top:2px;">Find a place that fits your life. (PGs, Hostels, 1/2 BHK Flats & Homestays)</div>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeModal('addRentalModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_rental_listing" />
      <div class="modal-body">
        
        <div class="form-group">
          <label class="form-label">Property Title *</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. Sunny 1BHK Studio near FC Road" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Rental Type *</label>
            <select name="rental_type" class="form-control" required>
              <option value="long_term">Long-Term Standard Rental</option>
              <option value="pg_hostel">PG / Student Hostel</option>
              <option value="short_stay">Short-Stay / Boutique Homestay</option>
              <option value="coliving">Co-Living Space</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Property Category *</label>
            <select name="category" class="form-control" required>
              <option value="Flat / Apartment">Flat / Apartment</option>
              <option value="PG / Hostel">PG / Hostel</option>
              <option value="Independent House / Villa">Independent House / Villa</option>
              <option value="Boutique Homestay / Guest House">Boutique Homestay / Guest House</option>
              <option value="Studio Apartment">Studio Apartment</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City *</label>
            <input type="text" name="city" class="form-control" placeholder="e.g. Pune" value="Pune" required />
          </div>
          <div class="form-group">
            <label class="form-label">State / Region</label>
            <input type="text" name="state" class="form-control" placeholder="e.g. Maharashtra" value="Maharashtra" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group" style="flex:1.5;">
            <label class="form-label">Nearby Landmark *</label>
            <input type="text" name="landmark" class="form-control" placeholder="e.g. Near BMCC College" required />
          </div>
          <div class="form-group" style="flex:1;">
            <label class="form-label">Distance</label>
            <input type="text" name="landmark_distance" class="form-control" placeholder="e.g. 350 m" value="350 m" />
          </div>
          <div class="form-group" style="flex:1.2;">
            <label class="form-label">Locality / Sector</label>
            <input type="text" name="locality" class="form-control" placeholder="e.g. Deccan Gymkhana" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-location-crosshairs" style="color:var(--primary); margin-right:4px;"></i> Latitude (GPS)</label>
            <input type="number" step="0.000001" name="latitude" class="form-control" placeholder="e.g. 18.5204" value="18.5204" />
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-location-crosshairs" style="color:var(--primary); margin-right:4px;"></i> Longitude (GPS)</label>
            <input type="number" step="0.000001" name="longitude" class="form-control" placeholder="e.g. 73.8567" value="73.8567" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Rent Price *</label>
            <input type="text" name="price" class="form-control" placeholder="e.g. ₹14,500" value="₹12,000" required />
          </div>
          <div class="form-group">
            <label class="form-label">Price Unit</label>
            <select name="price_unit" class="form-control">
              <option value="/ month">/ month</option>
              <option value="/ night">/ night</option>
              <option value="/ bed / month">/ bed / month</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Security Deposit</label>
            <input type="text" name="deposit" class="form-control" placeholder="e.g. ₹20,000 or Nil" value="₹20,000" />
          </div>
          <div class="form-group">
            <label class="form-label">BHK / Sharing</label>
            <input type="text" name="bhk" class="form-control" placeholder="e.g. 1 BHK or Double Sharing" value="1 BHK" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Furnishing</label>
            <select name="furnishing" class="form-control">
              <option value="Fully Furnished">Fully Furnished</option>
              <option value="Semi-Furnished" selected>Semi-Furnished</option>
              <option value="Unfurnished">Unfurnished</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Preferred Tenant</label>
            <select name="preferred_tenant" class="form-control">
              <option value="All Welcome" selected>All Welcome</option>
              <option value="Students & Bachelors">Students & Bachelors</option>
              <option value="Working Professionals">Working Professionals</option>
              <option value="Families Only">Families Only</option>
              <option value="Girls Only">Girls Only</option>
              <option value="Boys Only">Boys Only</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Food Policy</label>
            <select name="food_rule" class="form-control">
              <option value="Non-Veg Allowed" selected>Non-Veg Allowed</option>
              <option value="Pure Veg Only">Pure Veg Only</option>
              <option value="Vegetarian Friendly">Vegetarian Friendly</option>
              <option value="Meals Included">Meals Included</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Curfew / Gate Rule</label>
            <select name="curfew_rule" class="form-control">
              <option value="No Curfew" selected>No Curfew (24x7 Entry)</option>
              <option value="10:30 PM Gate Curfew">10:30 PM Gate Curfew</option>
              <option value="11:00 PM Gate Curfew">11:00 PM Gate Curfew</option>
              <option value="10:00 PM Gate Curfew">10:00 PM Gate Curfew</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Host / Warden Name</label>
            <input type="text" name="host_name" class="form-control" placeholder="e.g. Ramesh Sharma" value="Verified Host" />
          </div>
          <div class="form-group">
            <label class="form-label">Host Phone</label>
            <input type="text" name="host_phone" class="form-control" placeholder="e.g. +91 98765 43210" value="+91 98765 43210" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fa-regular fa-image" style="color:var(--primary); margin-right:4px;"></i> Cover Image URL or Preset
          </label>
          <input type="text" name="image" id="addRentalImage" class="form-control" placeholder="Paste image URL or pick preset below" value="realestate_01.webp" />
          <select class="form-control" style="margin-top:6px; font-size:12.5px; color:#475569;" onchange="if(this.value){ document.getElementById('addRentalImage').value = this.value; }">
            <option value="">-- Or pick from rental photo presets --</option>
            <option value="realestate_01.webp">Modern 1BHK Apartment (realestate_01.webp)</option>
            <option value="realestate_03.webp">Heritage Haveli Suite (realestate_03.webp)</option>
            <option value="hotel_02.jpg">Fort Kochi Homestay (hotel_02.jpg)</option>
            <option value="hotel_01.jpg">Heritage Resort Room (hotel_01.jpg)</option>
            <option value="college_01.jpg">Student Living Hub (college_01.jpg)</option>
            <option value="wellness_02.jpg">Peaceful Riverside Stay (wellness_02.jpg)</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Property Description</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Describe the flat, rooms, kitchen amenities, distance to bus stop/metro, and neighbourhood atmosphere..."></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addRentalModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary"><i class="fa-solid fa-plus-circle"></i> Save Rental Property</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= Modal 4: Edit Rental Property ================= -->
<div class="modal-backdrop" id="editRentalModal" onclick="handleBackdropClick(event, 'editRentalModal')">
  <div class="modal-window" style="max-width:720px;">
    <div class="modal-header">
      <div>
        <h3 class="modal-title">
          <i class="fa-solid fa-pen-to-square" style="color:var(--primary);"></i> Edit Rental Property #<span id="editRentalIdDisplay"></span>
        </h3>
        <div style="font-size:12px; color:#64748b; margin-top:2px;">Find a place that fits your life.</div>
      </div>
      <button type="button" class="modal-close-btn" onclick="closeModal('editRentalModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="edit_rental_listing" />
      <input type="hidden" name="id" id="editRentalId" />
      <div class="modal-body">
        
        <div class="form-group">
          <label class="form-label">Property Title *</label>
          <input type="text" name="title" id="editRentalTitle" class="form-control" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Rental Type *</label>
            <select name="rental_type" id="editRentalType" class="form-control" required>
              <option value="long_term">Long-Term Standard Rental</option>
              <option value="pg_hostel">PG / Student Hostel</option>
              <option value="short_stay">Short-Stay / Boutique Homestay</option>
              <option value="coliving">Co-Living Space</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Property Category *</label>
            <select name="category" id="editRentalCategory" class="form-control" required>
              <option value="Flat / Apartment">Flat / Apartment</option>
              <option value="PG / Hostel">PG / Hostel</option>
              <option value="Independent House / Villa">Independent House / Villa</option>
              <option value="Boutique Homestay / Guest House">Boutique Homestay / Guest House</option>
              <option value="Studio Apartment">Studio Apartment</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">City *</label>
            <input type="text" name="city" id="editRentalCity" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">State / Region</label>
            <input type="text" name="state" id="editRentalState" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group" style="flex:1.5;">
            <label class="form-label">Nearby Landmark *</label>
            <input type="text" name="landmark" id="editRentalLandmark" class="form-control" required />
          </div>
          <div class="form-group" style="flex:1;">
            <label class="form-label">Distance</label>
            <input type="text" name="landmark_distance" id="editRentalLandmarkDistance" class="form-control" placeholder="e.g. 350 m" />
          </div>
          <div class="form-group" style="flex:1.2;">
            <label class="form-label">Locality / Sector</label>
            <input type="text" name="locality" id="editRentalLocality" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-location-crosshairs" style="color:var(--primary); margin-right:4px;"></i> Latitude (GPS)</label>
            <input type="number" step="0.000001" name="latitude" id="editRentalLat" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-solid fa-location-crosshairs" style="color:var(--primary); margin-right:4px;"></i> Longitude (GPS)</label>
            <input type="number" step="0.000001" name="longitude" id="editRentalLng" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Rent Price *</label>
            <input type="text" name="price" id="editRentalPrice" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Price Unit</label>
            <select name="price_unit" id="editRentalPriceUnit" class="form-control">
              <option value="/ month">/ month</option>
              <option value="/ night">/ night</option>
              <option value="/ bed / month">/ bed / month</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Security Deposit</label>
            <input type="text" name="deposit" id="editRentalDeposit" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">BHK / Sharing</label>
            <input type="text" name="bhk" id="editRentalBhk" class="form-control" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Furnishing</label>
            <select name="furnishing" id="editRentalFurnishing" class="form-control">
              <option value="Fully Furnished">Fully Furnished</option>
              <option value="Semi-Furnished">Semi-Furnished</option>
              <option value="Unfurnished">Unfurnished</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Preferred Tenant</label>
            <select name="preferred_tenant" id="editRentalTenant" class="form-control">
              <option value="All Welcome">All Welcome</option>
              <option value="Students & Bachelors">Students & Bachelors</option>
              <option value="Working Professionals">Working Professionals</option>
              <option value="Families Only">Families Only</option>
              <option value="Girls Only">Girls Only</option>
              <option value="Boys Only">Boys Only</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Food Policy</label>
            <select name="food_rule" id="editRentalFoodRule" class="form-control">
              <option value="Non-Veg Allowed">Non-Veg Allowed</option>
              <option value="Pure Veg Only">Pure Veg Only</option>
              <option value="Vegetarian Friendly">Vegetarian Friendly</option>
              <option value="Meals Included">Meals Included</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Curfew / Gate Rule</label>
            <select name="curfew_rule" id="editRentalCurfew" class="form-control">
              <option value="No Curfew">No Curfew (24x7 Entry)</option>
              <option value="10:30 PM Gate Curfew">10:30 PM Gate Curfew</option>
              <option value="11:00 PM Gate Curfew">11:00 PM Gate Curfew</option>
              <option value="10:00 PM Gate Curfew">10:00 PM Gate Curfew</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Host / Warden Name</label>
            <input type="text" name="host_name" id="editRentalHostName" class="form-control" />
          </div>
          <div class="form-group">
            <label class="form-label">Host Phone</label>
            <input type="text" name="host_phone" id="editRentalHostPhone" class="form-control" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">
            <i class="fa-regular fa-image" style="color:var(--primary); margin-right:4px;"></i> Cover Image URL or Preset
          </label>
          <input type="text" name="image" id="editRentalImage" class="form-control" />
        </div>

        <div class="form-group">
          <label class="form-label">Property Description</label>
          <textarea name="description" id="editRentalDesc" class="form-control" rows="3"></textarea>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('editRentalModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary"><i class="fa-solid fa-check"></i> Update Rental Property</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= Modal 5: Add Category ================= -->
<div class="modal-backdrop" id="addCategoryModal" onclick="handleBackdropClick(event, 'addCategoryModal')">
  <div class="modal-window">
    <div class="modal-header">
      <h3 class="modal-title">
        <i class="fa-solid fa-folder-plus" style="color:var(--primary);"></i> Add Business Category
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addCategoryModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_category" />
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Category Name *</label>
          <input type="text" name="name" class="form-control" placeholder="e.g. Healthcare & Ayurvedic Wellness" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">URL Slug (Optional)</label>
            <input type="text" name="slug" class="form-control" placeholder="e.g. healthcare-wellness" />
          </div>
          <div class="form-group">
            <label class="form-label">Initial Count Text</label>
            <input type="text" name="count_text" class="form-control" value="0+ Listings" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">FontAwesome Icon Class</label>
          <input type="text" name="icon" class="form-control" value="fa-solid fa-heart-pulse" placeholder="e.g. fa-solid fa-hotel" />
        </div>

        <div class="form-group">
          <label class="form-label">Category Description</label>
          <textarea name="description" class="form-control" rows="2" placeholder="Brief summary of businesses in this category..."></textarea>
        </div>

        <div class="form-group" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 14px;">
          <label style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; font-size:13.5px; color:#1e293b; margin:0;">
            <input type="checkbox" name="show_in_signup" value="1" checked style="width:18px; height:18px; accent-color:var(--primary); cursor:pointer;" />
            <span><i class="fa-solid fa-user-plus" style="color:var(--primary); margin-right:4px;"></i> Show in User Sign Up Form</span>
          </label>
          <div style="font-size:12px; color:#64748b; margin-top:4px; margin-left:28px;">
            When checked, new users can select this category as an interest upon signing up on the website.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addCategoryModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary">Create Category</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= Modal 6: Add Location ================= -->
<div class="modal-backdrop" id="addLocationModal" onclick="handleBackdropClick(event, 'addLocationModal')">
  <div class="modal-window">
    <div class="modal-header">
      <h3 class="modal-title">
        <i class="fa-solid fa-map-pin" style="color:var(--primary);"></i> Add Destination / Location
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addLocationModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_location" />
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">City / Destination Name *</label>
          <input type="text" name="name" class="form-control" placeholder="e.g. Ahmedabad" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">State / Region</label>
            <input type="text" name="state" class="form-control" placeholder="e.g. Gujarat" />
          </div>
          <div class="form-group">
            <label class="form-label">URL Slug (Optional)</label>
            <input type="text" name="slug" class="form-control" placeholder="e.g. ahmedabad" />
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Count Display Text</label>
          <input type="text" name="count_text" class="form-control" value="0+ Listings" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addLocationModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary">Save Location</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= Modal 7: Add Blog ================= -->
<div class="modal-backdrop" id="addBlogModal" onclick="handleBackdropClick(event, 'addBlogModal')">
  <div class="modal-window">
    <div class="modal-header">
      <h3 class="modal-title">
        <i class="fa-solid fa-file-pen" style="color:var(--primary);"></i> Publish Blog Article
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('addBlogModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="add_blog" />
      <div class="modal-body">
        <div class="form-group">
          <label class="form-label">Article Title *</label>
          <input type="text" name="title" class="form-control" placeholder="e.g. Top 10 Hidden Gem Cafes in Old Delhi" required />
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Tag / Category</label>
            <input type="text" name="tag" class="form-control" value="Food & Culture" />
          </div>
          <div class="form-group">
            <label class="form-label">Author Name</label>
            <input type="text" name="author" class="form-control" value="POV Editorial Team" />
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Estimated Read Time</label>
            <input type="text" name="read_time" class="form-control" value="5 min read" />
          </div>
          <div class="form-group">
            <label class="form-label">Cover Image</label>
            <select name="image" class="form-control">
              <option value="hotel.webp">Kerala Waterfalls Cover (hotel.webp)</option>
              <option value="cafe.webp">Bangalore Student Life (cafe.webp)</option>
              <option value="service.webp">Indian Festivals Cover (service.webp)</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Short Excerpt</label>
          <textarea name="excerpt" class="form-control" rows="2" placeholder="Summary shown on article cards..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Full Article Content</label>
          <textarea name="body" class="form-control" rows="4" placeholder="Write full article body text here..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('addBlogModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary">Publish to MySQL</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= Dedicated Modal: Quick SEO Settings for Directory Listing ================= -->
<div class="modal-backdrop" id="seoListingModal" onclick="handleBackdropClick(event, 'seoListingModal')">
  <div class="modal-window" style="max-width: 680px;">
    <div class="modal-header" style="background: linear-gradient(135deg, #fdf4ff 0%, #fae8ff 100%); border-bottom: 1px solid #f0abfc;">
      <h3 class="modal-title" style="color:#701a75;">
        <i class="fa-solid fa-magnifying-glass-chart" style="color:#9333ea; margin-right:6px;"></i> SEO Optimization: <span id="quickSeoListingTitleDisplay" style="color:#0f172a; font-weight:700;"></span>
      </h3>
      <button type="button" class="modal-close-btn" onclick="closeModal('seoListingModal')">&times;</button>
    </div>
    <form method="POST" action="<?= htmlspecialchars(pov_url('admin-dashboard.php')) ?>">
      <input type="hidden" name="admin_action" value="quick_update_seo" />
      <input type="hidden" name="id" id="quickSeoListingId" />
      <input type="hidden" id="quickBaseListingTitle" />
      <input type="hidden" id="quickBaseListingDesc" />

      <div class="modal-body" style="padding:20px 24px;">
        
        <!-- Live Google SERP Preview Box -->
        <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:14px 18px; margin-bottom:20px; box-shadow:0 2px 6px rgba(0,0,0,0.04);">
          <div style="font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between;">
            <span><i class="fa-brands fa-google" style="color:#ea4335; margin-right:5px;"></i> Google Search Snippet Preview</span>
            <span style="color:#10b981; font-weight:700; font-size:11px; background:#ecfdf5; padding:2px 8px; border-radius:20px; border:1px solid #a7f3d0;"><i class="fa-solid fa-bolt"></i> Live SERP Preview</span>
          </div>
          <div style="font-family:arial,sans-serif;">
            <div style="font-size:12.5px; color:#202124; margin-bottom:3px; display:flex; align-items:center; gap:6px;">
              <span style="display:inline-block; width:16px; height:16px; border-radius:50%; background:#e2e8f0; text-align:center; font-size:10px; line-height:16px;">🇮🇳</span>
              <span>https://povindian.com › listings › <span id="quickPreviewSlug" style="color:#5f6368; font-weight:500;">listing-slug</span></span>
            </div>
            <div id="quickPreviewTitle" style="font-size:18px; color:#1a0dab; line-height:1.3; font-weight:400; margin-bottom:4px; cursor:pointer;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
              Directory Listing Title | POV Indian
            </div>
            <div id="quickPreviewDesc" style="font-size:13px; color:#4d5156; line-height:1.45;">
              Enter a meta description to preview how this directory listing appears on Google search results...
            </div>
          </div>
        </div>

        <!-- SEO Title -->
        <div class="form-group">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <label class="form-label" style="margin:0; font-weight:600;">SEO Meta Title <span style="font-weight:400; color:#64748b;">(&lt;title&gt; tag)</span></label>
            <span id="quickMetaTitleCounter" style="font-size:11.5px; color:#64748b; font-weight:500;">0 / 60 chars</span>
          </div>
          <input type="text" name="meta_title" id="quickMetaTitle" class="form-control" placeholder="e.g. Best Heritage Stay in Jaipur | POV Indian" oninput="povUpdateSeoLive('quick')" />
          <div style="font-size:11px; color:#94a3b8; margin-top:3px;">Recommended: 50–60 chars. If empty, the listing title + site name is used.</div>
        </div>

        <!-- Slug & Schema Type -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Custom URL Slug</label>
            <input type="text" name="slug" id="quickSlug" class="form-control" placeholder="e.g. jaipur-heritage-hotel" oninput="povUpdateSeoLive('quick')" />
          </div>
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Schema.org Rich Snippet</label>
            <select name="schema_type" id="quickSchemaType" class="form-control">
              <option value="LocalBusiness">LocalBusiness (General)</option>
              <option value="Hotel">Hotel / Accommodation</option>
              <option value="Restaurant">Restaurant / Café</option>
              <option value="MedicalClinic">Medical Clinic / Healthcare</option>
              <option value="EducationalOrganization">College / University</option>
              <option value="TravelAgency">Tourism & Travel Agency</option>
              <option value="RealEstateAgent">Real Estate Agency</option>
              <option value="Store">Store / Retail Business</option>
              <option value="HealthAndBeautyBusiness">Spa & Wellness Center</option>
            </select>
          </div>
        </div>

        <!-- Meta Description -->
        <div class="form-group">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
            <label class="form-label" style="margin:0; font-weight:600;">SEO Meta Description</label>
            <span id="quickMetaDescCounter" style="font-size:11.5px; color:#64748b; font-weight:500;">0 / 160 chars</span>
          </div>
          <textarea name="meta_description" id="quickMetaDesc" class="form-control" rows="3" placeholder="Write a compelling snippet for searchers that accurately describes the business and services (140-160 chars)..." oninput="povUpdateSeoLive('quick')"></textarea>
        </div>

        <!-- Meta Keywords & Canonical -->
        <div class="form-row">
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Target Focus Keywords</label>
            <input type="text" name="meta_keywords" id="quickMetaKeywords" class="form-control" placeholder="e.g. jaipur stays, heritage haveli, boutique rooms" />
          </div>
          <div class="form-group">
            <label class="form-label" style="font-weight:600;">Custom Canonical URL</label>
            <input type="text" name="canonical_url" id="quickCanonicalUrl" class="form-control" placeholder="https://povindian.com/listing-detail.php?id=..." />
          </div>
        </div>

        <!-- Open Graph Image -->
        <div class="form-group">
          <label class="form-label" style="font-weight:600;"><i class="fa-solid fa-share-nodes" style="color:#9333ea; margin-right:4px;"></i> Social Media Share Image (og:image)</label>
          <input type="text" name="og_image" id="quickOgImage" class="form-control" placeholder="https://... URL for WhatsApp, Telegram, Facebook previews" />
        </div>

      </div>
      <div class="modal-footer" style="background:#f8fafc; border-top:1px solid #e2e8f0;">
        <button type="button" class="btn-topbar btn-topbar-secondary" onclick="closeModal('seoListingModal')">Cancel</button>
        <button type="submit" class="btn-topbar btn-topbar-primary" style="background:#9333ea; border-color:#9333ea;">
          <i class="fa-solid fa-floppy-disk"></i> Save SEO Settings
        </button>
      </div>
    </form>
  </div>
</div>

