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
		$pastafisica = self::DIR_ROOT;
		$pastasup = '';
		$nomepasta = '';

		$nomepasta = isset($param['path']) ? trim($param['path']) : '';
		if($nomepasta == '.')
		{
			$nomepasta = '';
		}

		if($nomepasta != '')
		{
			// não deixa "sair" da pasta...
			$nomepasta = str_replace(['..','/'],['',''], $nomepasta);

			// mais de um nivel de pastas... ?
			$arr = explode('|',$nomepasta);
			$qty = count($arr);
			if($qty > 1)
			{
				$pastasup = $arr[ $qty - 2 ];
			}
			elseif($qty == 1)
			{
				$pastasup = reset($arr);
			}

			$nomepasta = str_replace('|', '/',$nomepasta);

			$pastafisica.= '/' . $nomepasta;
		}
		
		$vetor = [];

		// só lista arquivos desta extensão...?
		// $vExt = ['pdf','doc','odt','docx'];

		$ponteiro  = opendir( $pastafisica );
		while($nome = readdir($ponteiro))
		{
			if($nome != '.' and $nome != '..' and $nome != 'index.htm' and $nome != '.git')
			{
				$vetor[] = $nome;
			}
		}
		sort($vetor, SORT_STRING);


		if($pastasup != '')
		{
			// apontar para a pasta superior...
			echo "<a href='?path={$pastasup}'><img src='images/folder_up.png'> Voltar - Pasta Acima</a><br>";
		}

		$this->tabela = '<table width=575 border=1 style="border-collapse:collapse;">';

		// linha com título -> nome da pasta ou lista de pastas...
		$this->tabela.= '<tr>';

		if($nomepasta == '')
		{
			$this->tabela.= "<td style='background-color:#cacaca'><b>Pastas</b></td>";
		}
		else
		{
			$this->tabela.= "<td style='background-color:#cacaca'><b>{$nomepasta}</b></td>";
		}
		$this->tabela.= '</tr>';

		foreach($vetor as $i => $nome)
		{
			$this->tabela.= '<tr>';

			$teste = "{$pastafisica}/{$nome}";

			// pasta ou arquivo ?
			if (is_dir($teste))
			{
				// precisa remover o ponto . na frente...
				$pastax = str_replace('/','|',$teste);
				$pastax = str_replace('.|','',$pastax);
				$this->tabela.= "<td><a href='?path={$pastax}' ><img src='images/pasta.png'>{$nome}</a></td>";
			}
			elseif(file_exists($teste) and $nomepasta != '')
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