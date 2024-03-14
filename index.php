<?php

/**
 * 2021-05-28 - Apenas um único script PHP bem simples e genérico,
 * que lista todas as pastas, subpastas e arquivos PDF dentro 
 * da pasta onde ele (este script) estiver salvo.
 * 
 * Objetivo seria tentar facilitar a publicação 
 * de documentos no site da Prefeitura, 
 * mas falta pensar num jeito melhor de 
 * lançar estes arquivos pra dentro do servidor...
 * 
 * Passei o caminho das pastas por _GET mesmo
 * E usei | como separador de pastas
 * 
 */

$obj = new ListarPastasEArquivos();
$obj->onListar( $_GET );

class ListarPastasEArquivos
{
	const DIR_ROOT = '.';

	private $tabela;
	private $dirbase;

	public function __construct()
	{
		$css = "
		<style>
		table {
		  border-collapse: collapse;
		  width: 100%;
		}
		th, td {
		  padding: 8px;
		  text-align: left;
		  border-bottom: 1px solid #ddd;
		}
		tr:hover {background-color: #cacaca;}
		</style>";
		echo $css;

	}

	public function onListar($param)
	{
		$pastaFisica = self::DIR_ROOT;
		$pastaSuperior = '';
		$nomePasta = '';

		$nomePasta = isset($param['path']) ? trim($param['path']) : '';

		if($nomePasta == '.')
		{
			$nomePasta = '';
		}

		if($nomePasta != '')
		{
			// não deixa "sair" da pasta...
			$nomePasta = str_replace(['..','/'],['',''], $nomePasta);

			// mais de um nivel de pastas... ?
			$arrPastas = explode('|',$nomePasta);

			$qty = count($arrPastas);

			if($qty > 1)
			{
				$pastaSuperior = $arrPastas[ $qty - 2 ];
			}

			$nomePasta = str_replace('|', '/',$nomePasta);

			$pastaFisica.= "/{$nomePasta}";
		}
		
		$vetor = [];

		// só lista arquivos desta extensão...?
		// $vExt = ['pdf','doc','odt','docx'];

		$ponteiro  = opendir( $pastaFisica );
		while($nome = readdir($ponteiro))
		{
			if($nome != '.' and $nome != '..' and $nome != 'index.htm' and $nome != '.git' and $nome != 'images')
			{
				$vetor[] = $nome;
			}
		}
		sort($vetor, SORT_STRING);


		$this->tabela = '<table >';

		// linha com título -> nome da pasta ou lista de pastas...
		$this->tabela.= '<tr>';

		if($nomePasta == '')
		{
			$this->tabela.= "<td style='background-color:#cacaca'><b>Pastas</b></td>";
		}
		else
		{
			// apontar para a pasta superior ou raiz...
			echo "<a href='?path={$pastaSuperior}'><img src='images/folder_up.png'> Voltar - Pasta Acima</a><br>";

			$this->tabela.= "<td style='background-color:#cacaca'><b>{$nomePasta}</b></td>";
		}
		$this->tabela.= '</tr>';

		foreach($vetor as $i => $nome)
		{
			$this->tabela.= '<tr>';

			$teste = "{$pastaFisica}/{$nome}";

			// pasta ou arquivo ?
			if (is_dir($teste))
			{
				// precisa remover o ponto . na frente...
				$pastax = str_replace('/','|',$teste);
				$pastax = str_replace('.|','',$pastax);
				$this->tabela.= "<td><a href='?path={$pastax}' ><img src='images/pasta.png'>{$nome}</a></td>";
			}
			elseif(file_exists($teste) and $nomePasta != '')
			{
				// não ta listando arquivos na pasta "raíz",
				//	isto é, na mesma pasta do index.php...
				// link para o arquivo...
				$this->tabela.= "<td><a href='{$teste}' target='_blank'><img src='images/download.png'>{$nome}</a></td>";
			}
			$this->tabela.= '</tr>';
		}

		$this->tabela.= '</table>';

		echo $this->tabela;
	}

}
?>