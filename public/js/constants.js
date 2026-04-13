//Archivo para constantes utilizadas dentro de HOPE
const title = {
  system: "Mensaje del sistema",
  users: "Administración de Usuarios",
};


function getErrors(response) {
  if (response.status === 422) {
    let mensaje = "";
    for (let i in response.data.errors) {
      mensaje += response.data.errors[i] + "\n";
    }
    return mensaje;
  } else if (response.status === 419 || response.status === 401) {
    console.log("REDIRECCIÓN AL HOME ... ");
    return "Error 419/401";
  } else {
    console.log('message', response.data.message);
    return response.data.message ?? 'Error al obtener los datos.';
  }
}

//Mostrar Alerta
const showAlert = function (icon, title, text) {
  Swal.fire({
    icon: icon,
    title: title,
    text: text,
    confirmButtonColor: '#333'
  });
};

// $scope.showAlert('success', 'Titulo', 'Mensaje.');
// $scope.showAlert('error', 'Titulo', 'Mensaje.');
// $scope.showAlert('warning', 'Titulo', 'Mensaje.');