window.addEventListener('DOMContentLoaded', function() {// HTML
  const startHourSelect = document.getElementById('startHour');
  const startMinuteSelect = document.getElementById('startMinute');
  const endHourSelect = document.getElementById('endHour');
  const endMinuteSelect = document.getElementById('endMinute');
  
  // 時間のオプションを生成
  function generateTimeOptions() {
      const hours = Array.from({ length: 12 }, (_, i) => i + 1); // 1-12
      const minutes = [0, 15, 30, 45]; // 15分刻み

      // 開始時間のオプションを生成
      hours.forEach(hour => {
          const optionStart = document.createElement('option');
          optionStart.value = hour;
          optionStart.textContent = hour;
          startHourSelect.appendChild(optionStart);
      });

      // 終了時間のオプションを生成
      hours.forEach(hour => {
          const optionEnd = document.createElement('option');
          optionEnd.value = hour;
          optionEnd.textContent = hour;
          endHourSelect.appendChild(optionEnd);
      });

      // 分のオプションを生成
      minutes.forEach(minute => {
          const optionStartMinute = document.createElement('option');
          optionStartMinute.value = minute;
          optionStartMinute.textContent = minute.toString().padStart(2, '0'); // 2桁表示
          startMinuteSelect.appendChild(optionStartMinute);
      });

      // 終了分のオプションを生成
      minutes.forEach(minute => {
          const optionEndMinute = document.createElement('option');
          optionEndMinute.value = minute;
          optionEndMinute.textContent = minute.toString().padStart(2, '0'); // 2桁表示
          endMinuteSelect.appendChild(optionEndMinute);
      });
  }

  // 時間帯 (午前/午後) に応じて表示を変更
  const startTimePeriodSelect = document.getElementById('start-time-of-day');
  const endTimePeriodSelect = document.getElementById('end-time-of-day');
  
  startTimePeriodSelect.addEventListener('change', function() {
      updateTimePeriod('start', startTimePeriodSelect.value);
  });

  endTimePeriodSelect.addEventListener('change', function() {
      updateTimePeriod('end', endTimePeriodSelect.value);
  });

  // 時間帯に応じて時間を更新
  function updateTimePeriod(type, period) {
      const hourSelect = type === 'start' ? startHourSelect : endHourSelect;
      const hours = Array.from({ length: 12 }, (_, i) => i + 1); // 1-12

      // 時間帯に基づいて時間を12時間制にする
      hourSelect.innerHTML = ''; // リセット

      hours.forEach(hour => {
          const option = document.createElement('option');
          option.value = hour;
          option.textContent = hour + ' ' + period; // 午前/午後を追加
          hourSelect.appendChild(option);
      });
  }

  generateTimeOptions(); // ページが読み込まれたときに時間のオプションを生成
});
