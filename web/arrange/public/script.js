document.getElementById('add_availability').addEventListener('click', function() {
  var availabilitiesDiv = document.getElementById('availabilities');
  var newAvailability = document.createElement('div');
  newAvailability.classList.add('availability');
  
  newAvailability.innerHTML = `
      <label for="start_time">開始時間</label>
      <input type="datetime-local" name="availabilities[][start_time]" required><br>
      <label for="end_time">終了時間</label>
      <input type="datetime-local" name="availabilities[][end_time]" required><br>
  `;
  
  availabilitiesDiv.appendChild(newAvailability);
});
