const loginForm = document.querySelector("#login-form");
const cancel = document.querySelector("#cancel");

const username = document.querySelector("#username");
const password = document.querySelector("#password");

const type = document.querySelector("#type");
const message = document.querySelector("#message");

loginForm.addEventListener("click", function (e) {
  requestPost();
});
cancel.addEventListener("click", function (e) {
  window.location = "../../index.html";
});

/**
 * this is the button to send data to the server
 */
function requestPost() {
  /**
   * here will be the validation of the result
   * @returns if the server didn't responde corectly
   */
  const onRequstUpdate = function () {
    if (request.readyState < 4) {
      return;
    }
    if (
      (JSON.parse(request.responseText).error !== undefined)
    ) {
      MessageUI("Error: " + request.statusText, JSON.parse(request.responseText).error);
    } else if (request.status == 200 || request.status == 201) {
      MessageUI("Erfolg", "Du Bist Angemeldet");
    } else {
      MessageUI("Error: " + request.statusText, "Somthing went wrong, Contact the admin or try again with diffrent account information: " + JSON.parse(request.responseText).error);
    }
  };
  let request = new XMLHttpRequest();
  request.open("POST", "../../../../API/V1/Login");
  request.onreadystatechange = onRequstUpdate;
  const requestArray = {
    username: username.value,
    password: password.value,
  };
  request.send(JSON.stringify(requestArray));
}
