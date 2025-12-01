<?php
session_start();
include 'config.php';

$sql = "
    SELECT 
        p.id_prof, 
        p.nome, 
        p.areas, 
        COUNT(h.id_horario) as total_aulas
    FROM professor p
    LEFT JOIN horario h ON p.id_prof = h.id_prof
    WHERE p.status_aprovacao = 'APROVADO'
    GROUP BY p.id_prof
    ORDER BY total_aulas DESC
";

$result = mysqli_query($conexao, $sql);
$professores = mysqli_fetch_all($result, MYSQLI_ASSOC);

$limite_semanal = 30; 
?>

<div class="main-header">
    <h1>Relatório de Carga Horária Semanal</h1>
    <p>Metragem de aulas atribuídas por professor.</p>
</div>

<div class="listagem">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <?php 
        $top3 = array_slice($professores, 0, 3);
        $cores = ['#FFD700', '#C0C0C0', '#CD7F32'];
        $icones = ['fa-trophy', 'fa-medal', 'fa-medal'];
        
        foreach($top3 as $index => $top): 
            if($top['total_aulas'] == 0) continue;
        ?>
            <div class="card" style="background: white; padding: 20px; border-radius: 12px; border-left: 5px solid <?= $cores[$index] ?>; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 2rem; color: <?= $cores[$index] ?>;">
                    <i class="fa-solid <?= $icones[$index] ?>"></i>
                </div>
                <div>
                    <h3 style="margin: 0; color: #333; font-size: 1.1rem;"><?= htmlspecialchars($top['nome']) ?></h3>
                    <p style="margin: 5px 0 0 0; color: #666; font-weight: bold;"><?= $top['total_aulas'] ?> Aulas</p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Pos.</th>
                <th>Professor</th>
                <th>Áreas</th>
                <th>Carga Horária (Visual)</th>
                <th style="width: 100px; text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($professores as $i => $prof): 
                $percentual = min(100, ($prof['total_aulas'] / $limite_semanal) * 100);
                
                $cor_barra = '#48ab87'; 
                if($prof['total_aulas'] >= $limite_semanal) $cor_barra = '#e74c3c'; 
                elseif($prof['total_aulas'] >= ($limite_semanal * 0.8)) $cor_barra = '#f39c12'; 
            ?>
            <tr>
                <td><?= $i + 1 ?>º</td>
                <td style="font-weight: 500; color: #333;"><?= htmlspecialchars($prof['nome']) ?></td>
                <td style="font-size: 0.9em; color: #666;"><?= htmlspecialchars($prof['areas']) ?></td>
                <td>
                    <div style="width: 100%; background-color: #e0e0e0; border-radius: 10px; height: 20px; position: relative; overflow: hidden;">
                        <div style="width: <?= $percentual ?>%; background-color: <?= $cor_barra ?>; height: 100%; border-radius: 10px; transition: width 0.5s ease;"></div>
                        <span style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); font-size: 11px; color: #333; font-weight: bold; line-height: 20px;">
                            <?= number_format($percentual, 0) ?>%
                        </span>
                    </div>
                </td>
                <td style="text-align: center; font-weight: bold; font-size: 1.1em; color: #48ab87;">
                    <?= $prof['total_aulas'] ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>