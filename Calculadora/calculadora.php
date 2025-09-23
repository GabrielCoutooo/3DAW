<?php
$num1 = '';
$num2 = '';
$operacao = '';
$resultado = null;
$erros = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num1 = $_POST['num1'] ?? '';
    $num2 = $_POST['num2'] ?? '';
    $operacao = $_POST['operacao'] ?? '';
    if ($num1 === '' || $num2 === '') {
        $erros[] = "Por favor, preencha ambos os números.";
    } elseif (!is_numeric($num1) || !is_numeric($num2)) {
        $erros[] = "Por favor, insira apenas valores numéricos.";
    } elseif (!in_array($operacao, ['soma', 'subtracao', 'multiplicacao', 'divisao'])) {
        $erros[] = "Operação inválida.";
    } elseif ($operacao === 'divisao' && $num2 == 0) {
        $erros[] = "Não é possível dividir por zero.";
    }
    if (empty($erros)) {
        $num1_float = (float)$num1;
        $num2_float = (float)$num2;

        switch ($operacao) {
            case 'soma':
                $resultado = $num1_float + $num2_float;
                break;
            case 'subtracao':
                $resultado = $num1_float - $num2_float;
                break;
            case 'multiplicacao':
                $resultado = $num1_float * $num2_float;
                break;
            case 'divisao':
                $resultado = $num1_float / $num2_float;
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Calculadora PHP</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .calculadora {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 320px;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-top: 0;
        }

        .form-grupo {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        .form-grupo input,
        .form-grupo select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-grupo input {
            flex: 2;
        }

        .form-grupo select {
            flex: 1;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .resultado {
            background-color: #e9ecef;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 24px;
            font-weight: bold;
            color: #212529;
        }

        .erros {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: left;
        }

        .erros ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>

<body>

    <div class="calculadora">
        <h1>Calculadora PHP</h1>

        <?php if (!empty($erros)): ?>
            <div class="erros">
                <ul>
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-grupo">
                <input type="number" step="any" name="num1" placeholder="Primeiro número" value="<?= htmlspecialchars($num1) ?>" required>
                <input type="number" step="any" name="num2" placeholder="Segundo número" value="<?= htmlspecialchars($num2) ?>" required>
            </div>

            <div class="form-grupo">
                <select name="operacao" required>
                    <option value="soma" <?= $operacao === 'soma' ? 'selected' : '' ?>>+</option>
                    <option value="subtracao" <?= $operacao === 'subtracao' ? 'selected' : '' ?>>-</option>
                    <option value="multiplicacao" <?= $operacao === 'multiplicacao' ? 'selected' : '' ?>>×</option>
                    <option value="divisao" <?= $operacao === 'divisao' ? 'selected' : '' ?>>÷</option>
                </select>
            </div>

            <button type="submit">Calcular</button>
        </form>

        <?php if ($resultado !== null): ?>
            <div class="resultado">
                Resultado: <?= htmlspecialchars($resultado) ?>
            </div>
        <?php endif; ?>

    </div>

</body>

</html>
