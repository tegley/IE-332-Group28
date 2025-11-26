<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supply Chain Manager Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        @import "standardized_project_formatting.css";
        .filter-group { margin-bottom: 12px; }
    </style>
</head>
<body class="p-4">
<form>
<label for="DisruptionDropDown">Filter By:</label> <!-- How this is labeled allows it to be then found and stored as an ID later on-->
<select id="DisruptionDropDown" class="form-select mb-3">
  <option value="">Select Filter</option>
  <option value="company">Company Name</option>
  <option value="region">Region</option>
  <option value="regionTier">Region and Tier</option>
  <option value="tier">Tier</option>
</select>

<!-- Company filter -->
<div id="companyNameFilter" class="filter-group" style="display:none;">
  <label for="companyInput">Company Name:</label>
  <input type="text" id="companyInput" class="form-control" placeholder="Enter Company Name">
</div>

<!-- Region filter -->
<div id="regionChooser" class="filter-group" style="display:none;"> 
  <label for="regionSelect">Filter By:</label>
  <select id="regionSelect" class="form-select mb-3"> 
    <option value="">Select Continent or Country</option>
    <option value="continent">Continent</option>
    <option value="country">Country</option>
  </select>
</div>

<!-- Continent filter -->
<div id="continentFilter" class="filter-group" style="display:none;">
  <select id="continentSelect" class="form-select">
    <option value="" disabled selected>Select Continent</option>
    <option>Africa</option>
    <option>Antarctica</option>
    <option>Asia</option>
    <option>Australia/Oceania</option> 
    <option>Europe</option>
    <option>North America</option>
    <option>South America</option>
  </select>
</div>

<!-- Country filter -->
<div id="countryFilter" class="filter-group" style="display:none;"> 
  <label for="countryInput">Country Name:</label>
  <input type="text" id="countryInput" class="form-control mb-3" placeholder="Enter Country Name">
</div>

<!-- Tier filter -->
<div id="tierFilter" class="filter-group" style="display:none;">
  <label>Tier:</label>
  <select id="tierSelect" class="form-select">
    <option value="" disabled selected>Select Tier</option>
    <option>Tier 1</option>
    <option>Tier 2</option>
    <option>Tier 3</option>
  </select>
</div>

<!-- Region and Tier filter -->
<div id="regionTierFilter" class="filter-group" style="display:none;">
  <label>Tier:</label>
  <select id="regionTierSelect" class="form-select">
    <option value="" disabled selected>Select Tier</option>
    <option>Tier 1</option>
    <option>Tier 2</option>
    <option>Tier 3</option>
  </select>
</div>

<button type="submit" class="btn btn-primary">Submit</button>
</form>

<script>
    //allows values on the page to be stored as IDs so they can be found and easily kept track of
    //Note these lines will then point to which corresponding drop down the ID corresponds to. 
  const DisruptionDropDown = document.getElementById("DisruptionDropDown");  //const refers to a constant variable in javascript
  const companyNameFilter = document.getElementById("companyNameFilter");
  const regionChooser     = document.getElementById("regionChooser");
  const regionSelect      = document.getElementById("regionSelect");
  const continentFilter   = document.getElementById("continentFilter");
  const countryFilter     = document.getElementById("countryFilter");
  const tierFilter        = document.getElementById("tierFilter");
  const regionTierFilter  = document.getElementById("regionTierFilter");

  const companyInput      = document.getElementById("companyInput");
  const countryInput      = document.getElementById("countryInput");
  const continentSelect   = document.getElementById("continentSelect");
  const tierSelect        = document.getElementById("tierSelect");
  const regionTierSelect  = document.getElementById("regionTierSelect");

//addEventListener 
  DisruptionDropDown.addEventListener("change", function () {  // grouping drop downs into a function
    //  logging the selected value
    console.log("DisruptionDropDown selected:", this.value);
    companyNameFilter.style.display = "none"; //display nothing (hiding stuff)
    regionChooser.style.display = "none";
    continentFilter.style.display = "none";
    countryFilter.style.display = "none";
    tierFilter.style.display = "none";
    regionTierFilter.style.display = "none";

    // resets like these are necesary, since if they are not included then swapping between region and region and tier will carry values causing errors and glitches. 
    companyInput.value = "";
    regionSelect.selectedIndex = 0; //setting the Index back to zero automatically selects the first value in the dropdown. Again necessary to avoid issues.
    continentSelect.selectedIndex = 0;
    countryInput.value = "";
    tierSelect.selectedIndex = 0;
    regionTierSelect.selectedIndex = 0;

//if statements
    if (this.value === "company") { //for example, if company is selected. (the ID) 
      companyNameFilter.style.display = "block";  //then that corresponding dropdown will be displayed
    }
    if (this.value === "region") {
      regionChooser.style.display = "block";
    }
    if (this.value === "tier"){
      tierFilter.style.display ="block";
    }
    if (this.value === "regionTier"){
      regionChooser.style.display ="block";
      regionTierFilter.style.display ="block";
    }
  });

  // region function, this is necessary because it helps eliminate errors when carrying over data about regions.
  //since we have both select by region and also select by region and tier. It can cause errors.
  regionSelect.addEventListener("change", function () {
    console.log("RegionSelect selected:", this.value);
    continentFilter.style.display = "none";
    countryFilter.style.display = "none";

    // reset choices
    continentSelect.selectedIndex = 0;
    countryInput.value = "";

    if (this.value === "continent") {
      continentFilter.style.display = "block";
    }
    if (this.value === "country") {
      countryFilter.style.display = "block";
    }
  });
</script>
<!-- MOST UP TO DATE-->
</body>
</html>

