document.getElementById('loginForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const username = document.getElementById('username').value.trim();
  
  if (username === 'admin' || username === 'root') {
    window.location.href = '../admin/index.php';
  } else if (!isNaN(username) && username !== '') {
    document.getElementById('loginForm').action = 'login.php';
    document.getElementById('loginForm').method = 'POST';
    document.getElementById('loginForm').submit();
  } else {
    alert('Usuario incorrecto');
  }
});

