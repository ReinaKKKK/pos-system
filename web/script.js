document.getElementById('btn').addEventListener('click', generatePasswords);

function generatePasswords() {
    const lengthOptions = document.getElementsByName('passLength');
    let length = 12; // デフォルトの長さ
    for (const option of lengthOptions) {
        if (option.checked) {
            if (option.value === 'other') {
                length = parseInt(document.getElementById('customLength').value) || 12;
            } else {
                length = parseInt(option.value);
            }
        }
    }

    const includeLowercase = document.getElementById('includeLowercase').checked;
    const includeUppercase = document.getElementById('includeUppercase').checked;
    const includeNumbers = document.getElementById('includeNumbers').checked;
    const includeHyphen = document.getElementById('includeHyphen').checked;
    const includeUnderscore = document.getElementById('includeUnderscore').checked;
    const includeSymbols = document.getElementById('includeSymbols').checked;

    const charset = [
        ...(includeLowercase ? 'abcdefghijklmnopqrstuvwxyz'.split('') : []),
        ...(includeUppercase ? 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('') : []),
        ...(includeNumbers ? '0123456789'.split('') : []),
        ...(includeHyphen ? '-'.split('') : []),
        ...(includeUnderscore ? '_'.split('') : []),
        ...(includeSymbols ? '!#$%&()*+,-./:;<=>?@[\\]^_`{|}~'.split('') : []),
    ];

    const passwords = [];
    for (let i = 0; i < 100; i++) { // 最大100個のパスワードを生成
        let password = '';
        for (let j = 0; j < length; j++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }
        passwords.push(password);
    }

       // パスワードを2列に分けて表示
       const columns = document.querySelectorAll('.password-column');
       columns.forEach(column => {
           column.innerHTML = ''; // カラムをクリア
       });
   
       passwords.forEach((password, index) => {
           const columnIndex = index < 50 ? 0 : 1; // 50個ごとにカラムを切り替え
           const column = columns[columnIndex];
           const passwordBox = document.createElement('div');
           passwordBox.className = 'password-box';
           passwordBox.textContent = password;
           column.appendChild(passwordBox);
       });
   }