<?php

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="css/style.css"/>
  <title>Login</title>
</head>
<body>

<?php
// Exibir mensagem de sucesso/erro
if(isset($msg)){
    echo "<p style='color:green; text-align:center;'>$msg</p>";
}
?>

<video autoplay muted loop class="video-bg">
  <source src="img/background.mp4" type="video/mp4" />
  Seu navegador não suporta vídeo em HTML5.
</video>

<main class="container">

  <!-- Tela 1: Tem Conta? -->
  <div id="tela1">
    <form id="tela1-form">
      <div class="header-with-back">
        <button type="button" class="btn-voltar" style="display:none;"></button>
        <h1>Bem-vindo</h1>
      </div>
      <h2>Ao PlanIt!</h2>
      <button type="button" class="login" id="temContaBtn">Já tenho conta</button>
      <button type="button" class="login" id="naoTemContaBtn">Não tenho conta</button>
    </form>
  </div>

  <!-- Tela 2: Login -->
<div id="tela2" style="display:none;">
  <form id="login-form">
    <div class="header-with-back">
      <button type="button" class="btn-voltar" title="Voltar" aria-label="Voltar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6" />
        </svg>
      </button>
      <h1>Bem-vindo</h1>
    </div>
    <h2>De volta!</h2>

    <div class="input-box">
      <input type="text" id="cpf-login" name="cpf" placeholder="CPF" maxlength="14" required />
    </div>

    <div class="input-box">
      <input type="password" name="senha" placeholder="Senha" required />
    </div>

    <!-- Mensagem de status -->
    <div id="mensagemLogin" style="display:none; text-align:center; margin:5px 0;"></div>

    <!-- Botão de login -->
    <button type="submit" class="login">Login</button>

    <!-- Links depois do botão -->
    <div class="register-link">
      <a href="#" id="esqueciSenhaLink">Esqueci a senha</a>
    </div>
    <div class="register-link">
      <p>Não tem conta? <a href="#" id="voltarBtn">Cadastre-se</a></p>
    </div>
  </form>
</div>

  <!-- Tela 3: Escolha de Tipo (Aluno ou Professor) -->
  <div id="tela3" style="display:none;">
    <form id="tipo-form">
      <div class="header-with-back">
        <button type="button" class="btn-voltar" title="Voltar" aria-label="Voltar">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6" />
          </svg>
        </button>
        <h1>Bem-vindo</h1>
      </div>
      <h2>Ao PlanIt!</h2>
      <div class="custom-select-wrapper">
        <div class="custom-select" tabindex="0">Eu sou...</div>
        <div class="options">
          <div class="option" data-value="aluno">Aluno</div>
          <div class="option" data-value="professor">Professor</div>
        </div>
      </div>
      <input type="hidden" name="categoria" id="categoria" required />
      <button type="submit" class="login">Próximo</button>
      <div class="register-link">
        <p>Já tem conta? <a href="#" id="voltarParaLoginBtn">Entrar</a></p>
      </div>
    </form>
  </div>

 <!-- Tela 4: Cadastro de Aluno -->
<div id="tela4" style="display:none;">
  <form id="cadastro-aluno-form" method="POST" action="includes/cadastro.php">
    <input type="hidden" name="tipo" value="aluno">

    <div class="header-with-back">
      <button type="button" class="btn-voltar" title="Voltar" aria-label="Voltar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6" />
        </svg>
      </button>
      <h1>Bem-vindo</h1>
    </div>
    <h2>Aluno!</h2>

    <div class="input-box">
      <input type="text" name="nome" placeholder="Nome completo" required />
    </div>

    <div class="input-box">
      <input type="text" id="cpf-aluno" name="cpf" placeholder="CPF" maxlength="14" required />
    </div>

    <!-- Custom Select Série -->
    <div class="input-box">
      <div class="custom-select-wrapper">
        <div class="custom-select" tabindex="0">Selecione a Série</div>
        <div class="options">
          <div class="option" data-value="4">1º Ano</div>
          <div class="option" data-value="5">2º Ano</div>
          <div class="option" data-value="6">3º Ano</div>
        </div>
        <input type="hidden" name="id_serie" id="turma" required />
      </div>
    </div>

    <!-- Custom Select Curso -->
    <div class="input-box">
      <div class="custom-select-wrapper">
        <div class="custom-select" tabindex="0">Selecione o Curso</div>
        <div class="options">
          <div class="option" data-value="4">Desenvolvimento de Sistemas</div>
          <div class="option" data-value="5">Mecatrônica</div>
          <div class="option" data-value="6">Linguagens</div>
        </div>
        <input type="hidden" name="id_turma" id="curso" required />
      </div>
    </div>

    <!-- Senha -->
    <div class="input-box">
      <input type="password" name="senha" placeholder="Senha" required />
    </div>

    <!-- Confirmar senha -->
    <div class="input-box">
      <input type="password" name="confirmar_senha" placeholder="Confirmar senha" required />
    </div>

    <!-- Mensagem de status -->
    <div id="mensagem" style="display:none; text-align:center; margin:5px 0;"></div>

    <!-- Botão de cadastro -->
    <button type="button" class="login" id="btnCadastrarAluno">Cadastrar</button>
  </form>
</div>



<!-- Tela 5: Cadastro de Professor -->
<div id="tela5" style="display:none;">
  <form id="cadastro-professor-form" method="POST" action="includes/cadastro.php">
    <input type="hidden" name="tipo" value="professor">

    <div class="header-with-back">
      <button type="button" class="btn-voltar" title="Voltar" aria-label="Voltar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6" />
        </svg>
      </button>
      <h1>Bem-vindo</h1>
    </div>
    <h2>Professor!</h2>

    <div class="input-box">
      <input type="text" name="nome" placeholder="Nome completo" required />
    </div>

    <div class="input-box">
      <input type="text" id="cpf-professor" name="cpf" placeholder="CPF" maxlength="14" required />
    </div>

    <!-- Multi-select Áreas -->
    <div class="multi-select-wrapper">
      <div class="multi-select">Área(s) que você ensina...</div>
      <div class="options">
        <div class="option" data-value="Humanas">Ciências Humanas e Sociais</div>
        <div class="option" data-value="Exatas">Ciências Exatas</div>
        <div class="option" data-value="Biologicas">Ciências Biológicas</div>
        <div class="option" data-value="Linguagens">Linguagens e Comunicação</div>
        <div class="option" data-value="Informatica">Informática / TI</div>
        <div class="option" data-value="Administracao">Administração e Negócios</div>
        <div class="option" data-value="Saude">Saúde</div>
        <div class="option" data-value="Engenharia">Engenharia e Tecnologia</div>
        <div class="option" data-value="Design">Design e Comunicação Visual</div>
        <div class="option" data-value="MeioAmbiente">Meio Ambiente e Agroindústria</div>
        <div class="option" data-value="Moda">Moda e Estética</div>
      </div>
      <input type="hidden" name="areas" id="areas" />
    </div>

    <!-- Senha -->
    <div class="input-box">
      <input type="password" name="senha" placeholder="Senha" required />
    </div>

    <!-- Confirmar senha -->
    <div class="input-box">
      <input type="password" name="confirmar_senha" placeholder="Confirmar senha" required />
    </div>

    <!-- Mensagem de status -->
    <div id="mensagemProfessor" style="display:none; text-align:center; margin:5px 0;"></div>

    <!-- Botão de cadastro -->
    <button type="button" class="login" id="btnCadastrarProfessor">Cadastrar</button>
  </form>
</div>





<!-- Tela Esqueci a Senha -->
<div id="telaEsqueciSenha" style="display:none;">
  <form id="form-esqueci-senha">
    <div class="header-with-back">
      <button type="button" class="btn-voltar" title="Voltar" aria-label="Voltar">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="15 18 9 12 15 6" />
        </svg>
      </button>
      <h1>Redefinição de senha</h1>
    </div>
    <h2>Esqueceu sua senha?</h2>
    <div class="input-box">
      <input type="email" id="emailReset" name="emailReset" placeholder="Digite seu e-mail" required />
    </div>
    <button type="button" id="btnEnviarCodigo" class="login">Enviar código</button>
    <br><br><br><br>
    <div class="input-box" style="margin-top: 20px;">
      <input type="text" id="codigoReset" name="codigoReset" placeholder="Digite o código recebido" required />
    </div>
    <div class="input-box">
      <input type="password" id="novaSenha" name="novaSenha" placeholder="Nova senha" required />
    </div>
    <button type="submit" class="login">Redefinir senha</button>
  </form>
</div>


  </main>

<script src="js/script.js"></script> <!-- seu outro script -->
<script src="js/validarSenha.js"></script> <!-- script de validação de senha -->
<script src="js/validarSenhaProfessor.js"></script>
<script src="js/login.js"></script><!-- script de login existente -->





</body>
</html>
