var counter = 5;
var intervalId = null;
function action()
{
  clearInterval(intervalId);
  document.getElementById("timer").innerHTML = "Redirection!";	
}
function timer()
{
  document.getElementById("timer").innerHTML = "Vous allez être redirigé vers l'index dans "+counter + " secondes";
  counter--;
}
function start()
{
  intervalId = setInterval(timer, 1000);
  setTimeout(action, counter * 1000);
}	

start();
setTimeout(function(){location.href="/index.php"} , 5000);   
