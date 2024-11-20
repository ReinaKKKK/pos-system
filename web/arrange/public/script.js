document.addEventListener('DOMContentLoaded', function () {
    const addButton = document.querySelector("button[type='button']:not(.remove-button)");
    if (addButton) {
        addButton.addEventListener('click', addTimeSlot);
    }
//終了時間が開始時間よりも後にならないように
document.getElementById("startTime").addEventListener("input", function() {
    let startTime = document.getElementById("startTime").value;
document.getElementById("endTime").min = startTime;  // startTimeより後に終了時間を設定
    });

//     const availabilitySlots = <?php echo json_encode($availabilitySlots); ?>;

// // 空き時間候補を表示する
// foreach ($availabilitySlots as $slot) {
//     echo '<option value="' . $slot['start_time'] . '">' . $slot['start_time'] . ' - ' . $slot['end_time'] . '</option>';
// }


    // 候補日時リスト
    let timeSlots = [];

    // 候補日時を追加
    function addTimeSlot() {
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        if (!startTime || !endTime) {
            alert('開始時間と終了時間を入力してください');
            return;
        }

        // 候補日時が重複しないか確認
        if (timeSlots.some(slot => slot.startTime === startTime && slot.endTime === endTime)) {
            alert('同じ候補日時が既に追加されています。');
            return;
        }

        timeSlots.push({ startTime, endTime });
        displayTimeSlots();
        updateTimeSlotsInput();
    }
// 空き時間候補を表示する
availabilitySlots.forEach(function(slot) {
    const option = document.createElement("option");
    option.value = slot.start_time;
    option.textContent = slot.start_time + " - " + slot.end_time;
    // 例: 任意の<select>要素に追加する場合
    document.getElementById("availabilitySelect").appendChild(option);
});
    // 候補日時を表示
    function displayTimeSlots() {
        const timeSlotContainer = document.getElementById('timeSlotContainer');
        timeSlotContainer.innerHTML = '';

        timeSlots.forEach((slot, index) => {
            const timeSlot = document.createElement('div');
            timeSlot.classList.add('timeSlot');
            timeSlot.innerHTML = `
                <p>候補日時: ${slot.startTime} ～ ${slot.endTime}</p>
            `;

            const removeButton = document.createElement('button');
            removeButton.textContent = '削除';
            removeButton.onclick = function () {
                timeSlots.splice(index, 1);
                displayTimeSlots();
                updateTimeSlotsInput();
            };

            timeSlot.appendChild(removeButton);
            timeSlotContainer.appendChild(timeSlot);
        });
    }

    // 候補日時のリストをフォームのhiddenフィールドに更新
    function updateTimeSlotsInput() {
        const timeSlotsInput = document.getElementById('timeSlotsInput');
        timeSlotsInput.value = JSON.stringify(timeSlots); // 配列をJSON形式で送信
    }
});
