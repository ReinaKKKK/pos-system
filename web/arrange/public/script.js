document.addEventListener('DOMContentLoaded', function () {
    const addTimeSlotButton = document.getElementById('btn'); 
    const timeSlots = [];
    const timeSlotContainer = document.getElementById('timeSlotContainer');
    const timeSlotsInput = document.getElementById('timeSlotsInput');
    // 候補日時を追加
    function addTimeSlot() {
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;
        
        timeSlots.push({ startTime, endTime });
        displayTimeSlots();
        updateTimeSlotsInput();
    }

// 候補日時を表示
function displayTimeSlots() {
    timeSlotContainer.innerHTML = '';
    timeSlots.forEach((slot, index) => {
        const timeSlot = document.createElement('div');
        timeSlot.classList.add('timeSlot');

        // 日付と時間のフォーマットを変更
        const formattedStartTime = formatDateTime(slot.startTime);
        const formattedEndTime = formatDateTime(slot.endTime);

        timeSlot.innerHTML = `<p>候補日時: ${formattedStartTime} ～ ${formattedEndTime}</p>`;

        const removeButton = document.createElement('button');
        removeButton.textContent = '削除';
        removeButton.onclick = () => {
            timeSlots.splice(index, 1);
            displayTimeSlots();
            updateTimeSlotsInput();
        };

        timeSlot.appendChild(removeButton);
        timeSlotContainer.appendChild(timeSlot);
    });
}
// 日付と時間を"YYYY-MM-DD HH:MM"形式にフォーマットする関数
function formatDateTime(dateTime) {
    const date = new Date(dateTime);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // 月は0始まりなので+1
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    
    return `${year}-${month}-${day} ${hours}:${minutes}`;
}
    // 候補日時リストをhiddenフィールドに更新
    function updateTimeSlotsInput() {
        timeSlotsInput.value = JSON.stringify(timeSlots);
    }

    // イベントリスナーを追加
    if (addTimeSlotButton) {
        addTimeSlotButton.addEventListener('click', addTimeSlot);
    }

      // URLからevent_idを取得
      const urlParams = new URLSearchParams(window.location.search);
      const eventId = urlParams.get('event_id'); // 'event_id'を取得
  
      // event_idを隠しフィールドに設定
      const eventIdInput = document.getElementById('eventIdInput');
      if (eventIdInput && eventId) {
          eventIdInput.value = eventId; // ここでhiddenフィールドにevent_idをセット
      }
  
      // console.logで確認
      console.log('イベントID:', eventId);

    // 必要に応じて他の処理を追加
    // 例: URLのカスタマイズ表示
});
