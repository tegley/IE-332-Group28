function ValidateLogin() {
    const username = document.Login.Username.value
    const password = document.Login.Password.value
    if (username == "") {
        alert ("Please provide a username!");
        document.Login.Username.focus();
        return false;
    }

    if (password == "") {
        alert ("Please provide your password!");
        document.Login.Password.focus() ;
        return false;
    }

    if (username.length() > 50) {
        alert ("Username can't be longer than 50 characters!");
        document.Login.Password.focus() ;
        return false;
    }

    if (password.length() > 255) {
        alert ("Username can't be longer than 255 characters!");
        document.Login.Password.focus() ;
        return false;
    }

    else {
      var object = "String"
      xhtpp = new XMLHttpRequest();
      xhtpp.onload = function(){
      if (this.readyState == 4 && this.status == 200) {
        object = this.responseText;
      }
      };
      xhtpp.open("GET", "check_login.php?q=" + encodeURIComponent(username,password), true);
      xhtpp.send();
      if (object == "SupplyChainManager") {
            window.location.href = "supply_chain_manager.html";
      }
      else {
        alert(object)
      }
      return true;
  }
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
