window.addEventListener('DOMContentLoaded', function() {
    const startHourSelect = document.getElementById('startHour');
    const startMinuteSelect = document.getElementById('startMinute');
    const endHourSelect = document.getElementById('endHour');
    const endMinuteSelect = document.getElementById('endMinute');
    const dateInput = document.getElementById('inputDate');
    const startTimePeriodSelect = document.getElementById('start-time-of-day');
    const endTimePeriodSelect = document.getElementById('end-time-of-day');
    const candidatesList = document.getElementById('candidates-list');
    const addButton = document.getElementById('btn');
    
    // 時間のオプション生成
    function generateTimeOptions() {
        const hours = Array.from({ length: 12 }, (_, i) => i + 1);
        const minutes = [0, 15, 30, 45];

        hours.forEach(hour => {
            const optionStart = document.createElement('option');
            optionStart.value = hour;
            optionStart.textContent = hour;
            startHourSelect.appendChild(optionStart);
            
            const optionEnd = document.createElement('option');
            optionEnd.value = hour;
            optionEnd.textContent = hour;
            endHourSelect.appendChild(optionEnd);
        });

        minutes.forEach(minute => {
            const optionStartMinute = document.createElement('option');
            optionStartMinute.value = minute;
            optionStartMinute.textContent = minute.toString().padStart(2, '0');
            startMinuteSelect.appendChild(optionStartMinute);

            const optionEndMinute = document.createElement('option');
            optionEndMinute.value = minute;
            optionEndMinute.textContent = minute.toString().padStart(2, '0');
            endMinuteSelect.appendChild(optionEndMinute);
        });
    }

    generateTimeOptions();

    // 候補日をリストに追加する関数
    function addCandidate() {
        const date = dateInput.value;
        const startTimePeriod = startTimePeriodSelect.value;
        const endTimePeriod = endTimePeriodSelect.value;
        const startHour = startHourSelect.value;
        const startMinute = startMinuteSelect.value;
        const endHour = endHourSelect.value;
        const endMinute = endMinuteSelect.value;

        if (!date || !startHour || !startMinute || !endHour || !endMinute) {
            alert("日付と時間をすべて選択してください。");
            return;
        }

        const candidateText = `${date} - ${startTimePeriod} ${startHour}:${startMinute.padStart(2, '0')} から ${endTimePeriod} ${endHour}:${endMinute.padStart(2, '0')}`;
        
        const listItem = document.createElement('li');
        listItem.textContent = candidateText;
        
        // 削除ボタンを作成して候補日に追加
        const deleteButton = document.createElement('button');
        deleteButton.textContent = "削除";
        deleteButton.addEventListener('click', () => {
            candidatesList.removeChild(listItem);
        });
        
        listItem.appendChild(deleteButton);  // 削除ボタンを候補日アイテムに追加
        candidatesList.appendChild(listItem);  // 候補日リストにアイテムを追加
    }


    addButton.addEventListener('click', addCandidate);
});
