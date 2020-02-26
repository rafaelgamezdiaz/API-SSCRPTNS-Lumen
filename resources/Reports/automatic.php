<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?></title>
</head>
<style>
    table {
        border-collapse: collapse;
        margin-top: 10px;
        width: 100%;
    }
    th, td {
        text-align: center;
        padding: 1px;
        font-size:11px ;
        width: fit-content !important;
    }
    tr:nth-child(even){background-color: #f2f2f2}

    th {
        background-color: lightseagreen;
        color: white;
    }
    h1{
        font-family: apple-system;
        font-size: 2em;
    }
    img {
        position: absolute !important;
        left: 30px !important;
        top: 30px !important;
        width: 85px !important;
    }
</style>
<body>
    <div>
        <div>
            <img src="<?= $logo ?>" alt="Logo" >
        </div>
        <div style="text-align: center;margin-top: 50px;">
            <h2>Reporte de Suscripciones</h2>
        </div>
    </div>
    <div>
        <table>
            <thead>
            <tr>
                <?php foreach ($index as $title => $value):?>
                    <th><?php echo $title ?></th>
                <?php endforeach ?>
            </tr>
            </thead>
            <tbody>
            <?php $total = 0; ?>
            <?php foreach ($data as $key):?>
                <tr>
                    <?php foreach ($index as $title):?>
                        <td><?php echo is_array($key) ? $key[$title] ?? null : $key->$title ?? null?></td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
            <tr>


            </tr>
            </tbody>
        </table>
    <h5 style="color: darkslategrey;">Total de suscripciones <small><?php echo $total_of_subscriptions; ?></small></h5>
    </div>
</body>
</html>

