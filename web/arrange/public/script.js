//submitFormButtonのイベントリスナー
document.getElementById('submitFormButton').addEventListener('click', async () => {
    try {
        const timeSlotsInput = document.getElementById('timeSlotsInput');
        const eventIdInput = document.getElementById('eventIdInput');
        const eventId = eventIdInput ? eventIdInput.value : null; 
        const timeSlots = timeSlotsInput ? JSON.parse(timeSlotsInput.value) : []; 

        // POSTリクエストでサーバーへデータ送信
        const response = await fetch('save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                event_name: 'サンプルイベント', // イベント名
                event_id: eventId, // イベントID
                timeSlots: timeSlots // 時間スロット
            }),
        });

        if (!response.ok) {
            throw new Error('イベント作成に失敗しました。');
        }

        const html = await response.text(); // サーバーのレスポンスを取得
        document.body.innerHTML = html; // レスポンスを画面に表示
    } catch (error) {
        console.error(error);
        alert('エラーが発生しました: ' + error.message);
    }
});

// 時間スロットの追加処理
document.getElementById('btn').addEventListener('click', function() {
    var startTime = document.getElementById('startTime').value;
    var endTime = document.getElementById('endTime').value;

    if (startTime && endTime) {
        var timeSlotContainer = document.getElementById('timeSlotContainer');
        var div = document.createElement('div');
        div.innerHTML = `開始時間: ${startTime} - 終了時間: ${endTime}`;
        timeSlotContainer.appendChild(div);

        var timeSlotsInput = document.getElementById('timeSlotsInput');
        var timeSlots = timeSlotsInput.value ? JSON.parse(timeSlotsInput.value) : [];
        timeSlots.push({ startTime: startTime, endTime: endTime });
        timeSlotsInput.value = JSON.stringify(timeSlots);
    }
});

// 候補日時の重複チェック
function validateTimeSlots(timeSlots) {
    const errors = [];
    for (let i = 0; i < timeSlots.length; i++) {
        const start = new Date(timeSlots[i].startTime);
        const end = new Date(timeSlots[i].endTime);
        for (let j = i + 1; j < timeSlots.length; j++) {
            const compareStart = new Date(timeSlots[j].startTime);
            const compareEnd = new Date(timeSlots[j].endTime);
            if (start < compareEnd && end > compareStart) {
                errors.push(`候補日時 ${i + 1} と ${j + 1} が重複しています。`);
            }
        }
    }
    return errors;
}

//時間スロットの表示
function displayTimeSlots() {
    timeSlotContainer.innerHTML = ''; // 既存の時間スロットをクリア
    timeSlots.forEach((slot, index) => {
        const timeSlot = document.createElement('div');
        timeSlot.classList.add('timeSlot');

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
//日付と時間のフォーマット
function formatDateTime(dateTime) {
    const date = new Date(dateTime);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}`;
}
