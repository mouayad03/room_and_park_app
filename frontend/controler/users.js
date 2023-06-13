const usersTable = document.querySelector("#users");

const usernameA = document.querySelector("#username");
const email = document.querySelector("#email");
const password = document.querySelector("#pwd");

const radioS = document.querySelector("#radio-s");
const radioA = document.querySelector("#radio-a");

const onRequstUpdate = function() {
    if (request.readyState < 4) {
        return;
    }
    if (
        request.status == 400 ||
        request.status == 401 ||
        request.status == 404 ||
        request.status == 403
    ) {
        MessageUI(
          "Error",
          "Daten Konnten Nicht Geholt werden, Es Gibt keine oder du bist kein Admin"
        );
    } else if (
        request.status == 500 ||
        request.status == 501
    ) {
        MessageUI(
          "Server Error: " + request.statusText,
          "Der Server Hat einen Kritischen Fehler erliten: " +
          request.responseText
        );
    } else {
        usersTable.innerHTML = `
        <tr>
            <th>Benutzer name</th>
            <th>Löschen</th>
        </tr>
        `;
        JSON.parse(request.responseText).forEach(element => {
            const NEW_ROW = document.createElement("tr");

            const NAME = document.createElement("td");
            const EDIT = document.createElement("td");

            NAME.innerText = element.name;

            EDIT.innerHTML = `
                <button onclick="deleteUser('${element.name}')">Delete</button>
            `;

            NEW_ROW.appendChild(NAME);
            NEW_ROW.appendChild(EDIT);

            usersTable.appendChild(NEW_ROW);
        });
    }
}

const request = new XMLHttpRequest();
request.open("GET", "../../../../API/V1/Users");
request.onreadystatechange = onRequstUpdate;
request.send();

document.querySelector("#create-object").addEventListener("click", function(e){
    const onRequstCreate = function() {
        if (requestCreate.readyState < 4) {
            return;
        }
        if (
            requestCreate.status == 400 ||
            requestCreate.status == 401 ||
            requestCreate.status == 404 ||
            requestCreate.status == 403
        ) {
            MessageUI(
              "Error " + requestCreate.statusText,
              "Daten Konnten Nicht Gelöscht werden, Es Gibt keine oder du bist kein Admin" + requestCreate.responseText
            );
        } else if (
            requestCreate.status == 500 ||
            requestCreate.status == 501
        ) {
            MessageUI(
              "Server Error: " + requestCreate.statusText,
              "Der Server Hat einen Kritischen Fehler erliten: " +
              requestCreate.responseText
            );
        } else {
            MessageUI("Erfolg", "Der Benutzer wurde erfolgreich gelöscht");
        }
    }

    const requestCreate = new XMLHttpRequest();
    requestCreate.open("POST", "../../../../API/V1/User");
    requestCreate.onreadystatechange = onRequstCreate;

    // Source: https://stackoverflow.com/questions/1531093/how-do-i-get-the-current-date-in-javascript
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    today = mm + '-' + dd + '-' + yyyy;

    let type = "";

    if(radioS.checked === true) {
        type = "S";
    } else if (radioA.checked === true) {
        type = "A";
    } else {
        type = "D";
    }

    const data = {
        name: usernameA.value,
        email: email.value,
        password: password.value,
        type: type,
        add_date: today,
    };

    console.log(data);

    requestCreate.send(JSON.stringify(data));
});

function deleteUser(name) {
    const onRequstDelete = function() {
        if (requestDelete.readyState < 4) {
            return;
        }
        if (
            requestDelete.status == 400 ||
            requestDelete.status == 401 ||
            requestDelete.status == 404 ||
            requestDelete.status == 403
        ) {
            MessageUI(
              "Error",
              "Daten Konnten Nicht Gelöscht werden, Es Gibt keine oder du bist kein Admin"
            );
        } else if (
            requestDelete.status == 500 ||
            requestDelete.status == 501
        ) {
            MessageUI(
              "Server Error: " + requestDelete.statusText,
              "Der Server Hat einen Kritischen Fehler erliten: " +
              requestDelete.responseText
            );
        } else {
            MessageUI("Erfolg", "Der Benutzer wurde erfolgreich gelöscht");
        }
    }

    const requestDelete = new XMLHttpRequest();
    requestDelete.open("DELETE", "../../../../API/V1/User/" + name);
    requestDelete.onreadystatechange = onRequstDelete;
    requestDelete.send();
}
