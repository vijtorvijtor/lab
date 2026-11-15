function filtraTabla()
{
var busqueda = document.getElementById('filtro');
var aa=11;
var table = document.getElementById("idTabla").tBodies[0];
buscaTabla = function(){
  texto = busqueda.value.toLowerCase();
  var r=1;
  while(row = table.rows[r++])
  {
    if ( row.innerText.toLowerCase().indexOf(texto) !== -1 )
      row.style.display = null;
    else
      row.style.display = 'none';
  }
}
busqueda.addEventListener('mouseout', buscaTabla);
}

function filtraGrad()
{
console.log("Dentrod e filtraGrad");    
var busqueda = document.getElementById('filtro');
texto= busqueda.value.toLowerCase();
const searchTerms = texto.split(' '); // Divide la búsqueda en términos individuales
const rows = document.querySelectorAll('#idTabla tbody tr'); // Selecciona todas las filas de la tabla
if (texto===""){
    rows.forEach(row => {
      row.style.display = ''; // Muestra todas las filas
    });
    return; // Termina la ejecución de la función
  }


  rows.forEach(row => {

const cells = row.querySelectorAll('td'); // Selecciona todas las celdas de la fila
    let matchAll = true; // Asume que la fila coincide con todos los términos

    searchTerms.forEach(term => {
      if (term.startsWith('+') || term.startsWith('-')) {
        // Busca términos que comienzan con + o - (números con signo)
        let termFound = false;

        cells.forEach(cell => {
          if (cell.textContent.toLowerCase().includes(term)) {
            termFound = true;
          }
        });

        if (!termFound) {
          matchAll = false; // Si no se encuentra el término, la fila no coincide
        }
      } else {
        // Busca términos generales (sin signo)
        let termFound = false;

        cells.forEach(cell => {
          if (cell.textContent.toLowerCase().includes(term)) {
            termFound = true;
          }
        });

        if (!termFound) {
          matchAll = false; // Si no se encuentra el término, la fila no coincide
        }
      }
    });

    // Muestra u oculta la fila dependiendo de si coincide con todos los términos
    if (matchAll) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
} 


function enciendeBotones(){
     var oChk = document.getElementById('idCheckEjecuta');
  if (oChk.checked != true)
  {
    document.getElementById('idBtnNuevo').disabled=true;
    document.getElementById('idBtnEditar').disabled=true;
    document.getElementById('idBtnEliminar').disabled=true;
  }
  else{
    document.getElementById('idBtnNuevo').disabled=false;
    document.getElementById('idBtnEditar').disabled=false;
    document.getElementById('idBtnEliminar').disabled=false;
  }
}

function enciendeMenu(){
   var obj=arguments[0];
   //alert("dentro de eniendeMenu()");
   document.getElementById(obj).style.backgroundColor='yellow';
   
} 

function propSelect2(){

   var defaultMatcher = $.fn.select2.defaults.defaults.matcher;
   function customMatcher(params, data) {
     if (params.term && data.id == "ALL") 
      return null;
     return defaultMatcher(params, data);
   }

   $.fn.select2.defaults.set('matcher: customMatcher', function(params, data) {
      if ($.trim(params.term) === '') {
         return data;
      }
      if (typeof data.text === 'undefined') {
         return null;
      }
      var words = params.term.toUpperCase().split(" ");
      for (var i = 0; i < words.length; i++) {
         if (data.text.toUpperCase().indexOf(words[i]) < 0) {
            return null;
         }
      }
      return data;
   });


}

