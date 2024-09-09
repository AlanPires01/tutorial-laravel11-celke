<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    public function index(){
        $users = User::get();

        return view('users.index',['users'=>$users]);
    }

    public function import(Request $request){
        //Valiar o arquivo
        $request->validate([
            'file'=>'required | mimes:csv,txt|max:2024',           
        ],[
            'file.required'=>'o campo arquivo é obrigatório',
            'file.mimes'=>'Arquivo inválido, necessário enviar arquivo .CSV',
            'file.max' => 'Tamanho do arquivo excede :max Mb'
        ]);
        //Criar o array com as colunas do banco de dados
        $headers=['name','email','password'];

        //Recebr o arquivo, ler os dados e converter as strings em array
       $dataFile = array_map('str_getcsv',file($request->file('file')));
        
       //Quantidade de registros implementados
       $numberRegisteredRecords = 0;

       foreach ($dataFile as $keyData => $row  ) {
            //COnverter a linha em array
            $values = explode(';',$row[0]);
        
            //Percorrer as colunas do cabeçalho

            foreach($headers as $key=>$header){
               //Atribuir o valor ao elemento do array
                $arrayValues[$keyData][$header] = $values[$key];
            }
            $numberRegisteredRecords++;

           

        }

        //Cadastrar registros no banco de dados
        User::insert($arrayValues);
        
        return back()->with('success','Dados importados com sucesso. <br> Quantidade:'. $numberRegisteredRecords);
       
    }
}
