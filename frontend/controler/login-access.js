const namePlace = document.querySelector("#name");

// The login button in the header can log you out or send you to the login page

const loginButton = document.querySelector("#login");

loginButton.addEventListener("click", function (e) {
  if (namePlace.innerText == "Bitte Logen sie sich Ein!") {
    window.location.href = "/frontend/view/login.html";
  } else {
    document.cookie =
      "token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/API/V1;";
    window.location.href = "/";
  }
});

// Display your name when Logged In

function requesting() {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
    const response = JSON.parse(request.responseText);

    if (response.type == "A") {
      namePlace.innerText = "Admin: " + response.name;
    } else if (response.type == "S") {
      namePlace.innerText = "Sekretariat: " + response.name;
    } else if (response.type == "D") {
      namePlace.innerText = "Dozent: " + response.name;
    } else {
      namePlace.innerText = "Bitte Logen sie sich Ein!";
    }
  };

  const request = new XMLHttpRequest();
  request.open("GET", "../../../../API/V1/WhoAmI");
  request.onreadystatechange = onRequstUpdate;
  request.send();
}

requesting();

function MessageUI(head, information) {
  const thing = document.createElement("div");
  thing.className = "message-and-error";

  const header = document.createElement("h1");
  const informationField = document.createElement("p");

  header.innerText = head;
  informationField.innerText = information;

  thing.appendChild(header);
  thing.appendChild(informationField);

  const deleteOnClick = function () {
    this.parentNode.removeChild(this);
  };

  thing.addEventListener("click", deleteOnClick);

  document.body.appendChild(thing);
}

// source: https://stackoverflow.com/questions/31106096/checking-if-specific-console-error-occurred-exists-with-javascript
/*
let original = window.console
window.console += {
    error: function(){

        //Gets text from error message.
        errorText = arguments['0'];

        if (errorText.includes('TimeEvent.COMPLETE')) {
            MessageUI("error", errorText);
        }

        original.error.apply(original, arguments);
    }
}
*/
