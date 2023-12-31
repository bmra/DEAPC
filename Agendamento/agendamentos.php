<!DOCTYPE html>
<html>
<head>
    <title>Pagina de Agendamentos</title>
    <style>
     body {
  background-color: #f2f2f2;
  font-family: Arial, sans-serif;
}

h1 {
  text-align: center;
  margin-top: 30px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

table, th, td {
  border: 1px solid black;
  padding: 8px;
  text-align: center;
}

form {
  margin-top: 20px;
  text-align: center;
}

label {
  display: block;
  margin-bottom: 10px;
}

input[type="text"],
input[type="date"],
input[type="time"] {
  width: 100%;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 3px;
  margin-bottom: 10px;
}

input[type="submit"] {
  background-color: #4CAF50;
  color: #fff;
  padding: 10px 20px;
  border: none;
  border-radius: 3px;
  cursor: pointer;
}

    </style>
</head>
<body>
    <h1>Agendamentos</h1>

    <?php
    //detalhes para a conexão á base de dados
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "medisep";

    // criar a conexão
    $connection = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // verificar se foi possível estabelecer uma conexão, senão enviar um erro 
    if ($connection->connect_error) {
        die("Falha ao estabelecer conexão: " . $connection->connect_error);
    }

    //Tratar da submissão do formulário para adicionar novo Agendamento
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_agendamento'])) { // apenas se for um POST e o form for para adicionar o agendaemnto
        $id_medico = isset($_POST['id_medico']) ? $_POST['id_medico'] : '';
        $id_paciente = isset($_POST['id_paciente']) ? $_POST['id_paciente'] : '';
        $data_agendamento = isset($_POST['data_agendamento']) ? $_POST['data_agendamento'] : '';
        $hora_agendamento = isset($_POST['hora_agendamento']) ? $_POST['hora_agendamento'] : '';
        $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : '';

        // inserir o agendamento na base de dados
        $query = "INSERT INTO agendamentos (id_medico, id_paciente, data_agendamento, hora_agendamento, descricao) VALUES ('$id_medico', '$id_paciente', $data_agendamento, '$hora_agendamento', '$descricao')";
        $result = mysqli_query($connection, $query);

        // se a query foi um sucesso, ou seja, se o resultado existe, mostrar uma mensagem de sucesso, senão mostrar mensagem de erro
        if ($result) {
            echo '<p>Novo Agendamento adicionado com sucesso.</p>';
        } else {
            echo '<p>!!! Erro !!!: ' . mysqli_error($connection) . '</p>';
        }
    }

    // tratar da submissão do formulário para remover agendamento
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_agendamento'])) { // apenas se for um POST e o botão "Remover" for clicado, ou seja, o form é o remove_agendamento
        $agendamento_id = $_POST['agendamento_id'];

        // query para remover o agendamento
        $query = "DELETE FROM agendamentos WHERE id = $agendamento_id";
        $result = mysqli_query($connection, $query);

        // se a query foi um sucesso, mostrar uma mensagem de sucesso, senão mostrar mensagem de erro
        if ($result) {
            echo '<p>Agendamento removido com sucesso.</p>';
        } else {
            echo '<p>!!! Erro !!!: ' . mysqli_error($connection) . '</p>';
        }
    }
    ?>

    <!-- Formulário para adicionar novo agendamento -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

        <label for="id_medico">ID do Medico:</label>
        <input type="text" id="id_medico" name="id_medico" required><br>

        <label for="id_paciente">ID do Paciente:</label>
        <input type="text" id="id_paciente" name="id_paciente" required><br>

        <label for="data_agendamento">Data do Agendamento:</label>
        <input type="date" id="data_agendamento" name="data_agendamento" required><br>

        <label for="hora_agendamento">Hora de Agendamento:</label>
        <input type="time" id="hora_agendamento" name="hora_agendamento" required><br>

        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" required><br>

        <input type="submit" name="add_agendamento" value="Submit">

    </form>

    <?php
    // query para obter toda a informação dos agendamentos da tabela agendamentos
    $query = "SELECT id, id_medico, id_paciente, data_agendamento, hora_agendamento, descricao FROM agendamentos";
    //executa a query
    $result = mysqli_query($connection, $query);

    // se a query foi um sucesso, mostrar os agendamentos encontrados na base de dados
    if ($result && mysqli_num_rows($result) > 0) {
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>ID Medico</th>';
        echo '<th>ID Paciente</th>';
        echo '<th>Data de Agendamento</th>';
        echo '<th>Hora de Agendamento</th>';
        echo '<th>Descrição</th>';
        echo '<th>Remover</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // iterar pelos agendamentos encontrados e inserir na tabela as linhas (tr) com as colunas (th)
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['id_medico'] . '</td>';
            echo '<td>' . $row['id_paciente'] . '</td>';
            echo '<td>' . $row['data_agendamento'] . '</td>';
            echo '<td>' . $row['hora_agendamento'] . '</td>';
            echo '<td>' . $row['descricao'] . '</td>';
            echo '<td>';
            echo '<form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">'; //php_self para chamar de novo a pagina php para atualizar o conteudo da lista
            echo '<input type="hidden" name="agendamento_id" value="' . $row['id'] . '">'; //input escondido para guardar o id do utilziador que se quer remover
            echo '<input type="submit" name="remove_agendamento" value="Remover">'; //butao para enviar o formulario de rmeover utilizador
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>Não existem agendamentos para apresentar.</p>';
    }

    // fechar a conexão com a base de dados
    mysqli_close($connection);
    ?>
</body>
</html>