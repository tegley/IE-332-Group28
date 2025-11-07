function ValidateLogin() {
    const username = document.Login.Username.value
    const password = document.Login.Password.value
    if (username == "") {  //Don't send PHP requests without a username
        alert ("Please provide a username!");
        document.Login.Username.focus();
        return false;
    }

    if (password == "") { //Don't send PHP requests without a password
        alert ("Please provide your password!");
        document.Login.Password.focus();
        return false;
    }

    if (username.length > 50) { //Reasonable username limits
        alert ("Username can't be longer than 50 characters!");
        document.Login.Username.focus();
        return false;
    }

    if (password.length > 255) { //Reasonable password limits
        alert ("Password can't be longer than 255 characters!");
        document.Login.Password.focus();
        return false;
    }

    //Success
    string = username + " " + password;
    xhttp = new XMLHttpRequest();
    xhttp.onload = function(){
    if (this.readyState === 4 && this.status === 200) {
      try {
        //Parse the JSON response text
        const my_object = JSON.parse(this.responseText);
        
        //1st Case - Username is incorrect
        if (my_object[1]=="Username is incorrect") {
          alert("Username is invalid")
        }
        //2nd Case - Password is incorrect
        if (my_object[1]=="Password is incorrect") {
          alert(`Hey ${my_object[0]} - your password is incorrect :(`);
        }

        //3rd Case - User is a SupplyChainManager
        if (my_object[0]=="SupplyChainManager") {
          window.location.href = "supplychainmanager.html";
        }

        //4th Case - User is a SeniorManager
        if (my_object[0]=="SeniorManager") {
          window.location.href = "seniormanager.html";
        }
      }
      catch (error) {
        //Handle JSON parsing errors if the server sends back invalid JSON
        alert("A server error occured: ", error.message);
      }
    }
  };
    xhttp.open("GET", "check_login.php?q=" + string, true);
    xhttp.send();
    return true;
}

function ValidateCompany() {
    const input = document.CompanyInformation.company_name.value 
    if (input == "") {
        alert ("Please provide a company!");
        document.CompanyInformation.company_name.focus();
        return false;
    }
    else {
    xhtpp = new XMLHttpRequest();
    xhtpp.onload = function(){
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("PHP").innerHTML = this.responseText;
      }
    };
    xhtpp.open("GET", "connect.php?q=" + encodeURIComponent(input), true);
    xhtpp.send();
  }

    return true;
}
//
