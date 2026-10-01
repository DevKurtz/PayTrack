(function(){
  try { console.log('[old-section-select.js] loaded'); } catch(e) {}
  document.addEventListener('DOMContentLoaded', function(){
    try { console.log('[old-section-select.js] DOM ready'); } catch(e) {}

    // Delegated click for View buttons
    document.addEventListener('click', function(ev){
      const btn = ev.target.closest('.view-section-btn');
      if (!btn) return;
      ev.preventDefault();
      try { console.log('[old-section-select.js] View clicked'); } catch(e) {}
      var sectionId = btn.getAttribute('data-section-id');
      var sectionName = btn.getAttribute('data-section-name');
      var modalEl = document.getElementById('viewSectionModal');
      var titleEl = document.getElementById('viewSectionModalLabel');
      var bodyEl  = document.getElementById('sectionSubjectsContent');
      if (titleEl) titleEl.textContent = 'Section: ' + sectionName;
      if (bodyEl) bodyEl.innerHTML = '<div class="text-center text-muted">Loading...</div>';
      var modal = (window.bootstrap && bootstrap.Modal) ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;
      if (modal) { modal.show(); } else if (modalEl) { modalEl.classList.add('show'); modalEl.style.display = 'block'; }
      // Fetch section subjects
      const studentId = document.querySelector('input[name="student_id"]').value || '';
      const csrfToken = document.querySelector('input[name="csrf_token"]').value || '';
      fetch('get-section-subjects.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'section_id=' + encodeURIComponent(sectionId)
            + '&student_id=' + encodeURIComponent(studentId)
            + '&csrf_token=' + encodeURIComponent(csrfToken)
      })
      .then(async response => {
        const text = await response.text();
        if (!response.ok) {
          try { const j = JSON.parse(text); throw new Error(j.message || text); } catch(e) { throw new Error(text); }
        }
        try { return JSON.parse(text); } catch(e) { throw new Error('Invalid server response'); }
      })
      .then(data => {
        if (!bodyEl) return;
        if (data.success && Array.isArray(data.subjects) && data.subjects.length) {
          let html = '<div class="table-responsive"><table class="table table-bordered table-striped"><thead><tr>' +
                     '<th>Subj Code</th><th>Name</th><th>Unit</th><th>Type</th><th>Schedule</th></tr></thead><tbody>';
          data.subjects.forEach(s => {
            const sched = [s.days, s.start_time, s.end_time, s.room, s.instructor].filter(Boolean).join(' ');
            html += '<tr><td>' + (s.subj_code||'') + '</td><td>' + (s.subj_name||'') + '</td><td>' + (s.subj_unit||'') + '</td>' +
                    '<td>' + (s.type||'') + '</td><td>' + sched + '</td></tr>';
          });
          html += '</tbody></table></div>';
          bodyEl.innerHTML = html;
        } else {
          bodyEl.innerHTML = '<div class="alert alert-info">No subjects found for this section.</div>';
        }
      })
      .catch(err => {
        try { console.error('[old-section-select.js] fetch error', err); } catch(e) {}
        if (bodyEl) bodyEl.innerHTML = '<div class="alert alert-danger">Failed to load section subjects: ' + (err && err.message ? err.message : 'Unknown error') + '</div>';
      });
    });

    // Delegated tab activation
    document.addEventListener('click', function(e){
      const btn = e.target.closest('#enrollmentTabs [data-bs-toggle="tab"]');
      if (!btn) return;
      e.preventDefault();
      const targetSelector = btn.getAttribute('data-bs-target');
      if (window.bootstrap && bootstrap.Tab) {
        bootstrap.Tab.getOrCreateInstance(btn).show();
        return;
      }
      document.querySelectorAll('#enrollmentTabs [data-bs-toggle="tab"]').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active','show'));
      const pane = document.querySelector(targetSelector);
      if (pane) { pane.classList.add('active','show'); }
    });

    // Irregular: checkbox to section dropdown loader
    function loadSectionsForSubject(subjectId, dropdown) {
      dropdown.innerHTML = '<option value="">Loading...</option>';
      const studentId = document.querySelector('input[name="student_id"]').value || '';
      const csrfToken = document.querySelector('input[name="csrf_token"]').value || '';
      fetch('get-subject-sections.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'subject_id=' + encodeURIComponent(subjectId)
            + '&student_id=' + encodeURIComponent(studentId)
            + '&csrf_token=' + encodeURIComponent(csrfToken)
      })
      .then(async response => {
        const text = await response.text();
        try { return JSON.parse(text); } catch(e) { throw new Error(text || 'Invalid server response'); }
      })
      .then(data => {
        dropdown.innerHTML = '<option value="">Select Section</option>';
        if (data.success && Array.isArray(data.sections) && data.sections.length) {
          data.sections.forEach(function(section){
            const option = document.createElement('option');
            option.value = section.id;
            option.textContent = section.section_name + ' (' + section.available_slots + ' slots available)';
            dropdown.appendChild(option);
          });
        } else {
          const option = document.createElement('option');
          option.value = '';
          option.textContent = (data && data.message) ? data.message : 'No sections available';
          option.disabled = true;
          dropdown.appendChild(option);
        }
      })
      .catch(err => {
        try { console.error('[old-section-select.js] irregular fetch error', err); } catch(e) {}
        dropdown.innerHTML = '';
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Error loading sections';
        option.disabled = true;
        dropdown.appendChild(option);
      });
    }

    // Bind irregular checkboxes
    document.querySelectorAll('.subject-checkbox').forEach(function(checkbox){
      checkbox.addEventListener('change', function(){
        const subjectId = this.getAttribute('data-subject-id');
        const dropdown = document.querySelector('.section-dropdown[data-subject-id="' + subjectId + '"]');
        if (!dropdown) return;
        if (this.checked) {
          dropdown.disabled = false;
          loadSectionsForSubject(subjectId, dropdown);
        } else {
          dropdown.disabled = true;
          dropdown.innerHTML = '<option value="">Select Section</option>';
        }
      });
    });

    // Initialize pre-checked
    document.querySelectorAll('.subject-checkbox:checked').forEach(function(checkbox){
      const subjectId = checkbox.getAttribute('data-subject-id');
      const dropdown = document.querySelector('.section-dropdown[data-subject-id="' + subjectId + '"]');
      if (dropdown) {
        dropdown.disabled = false;
        loadSectionsForSubject(subjectId, dropdown);
      }
    });
  });
})();
