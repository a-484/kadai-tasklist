<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task; 

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    // getでtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        $data = [];
        
        //ログインしているかチェック
        if (\Auth::check()) {
            
            // 認証済みユーザを取得
            $user = \Auth::user();
            
            //タスクデータを取得
            $tasks = $user->tasks()->get();
            
            //配列に格納
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            
            return view('tasks.index', $data);
        
        //ログインしていない場合
        }else{
            
            //ログイン画面を表示
            return view('auth.login');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    //新規タスク作成画面を表示するため
    public function create()
    {
        //ログインしているかチェック
        if (\Auth::check()) {
            
            $task = new Task;
    
            //タスク作成ビューを表示
            return view('tasks.create', [
                'task' => $task,
            ]);
            
        
        }else{
            
            return view('auth.login');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
     
    //新規タスクを追加された場合
    public function store(Request $request)
    {
        //ログインしているかチェック
        if (\Auth::check()) {
            
            // バリデーション
            $request->validate([
                'content' => 'required|max:255',
                'status' => 'required|max:10'
            ]);
    
            //タスクを作成
            $request->user()->tasks()->create([
                'status' => $request->status,
                'content' => $request->content,
            ]);
            
            //トップページにリダイレクト
            return redirect('/');
            
        }else{
            
            return view('auth.login');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    //タスク詳細画面
    public function show($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        if(\Auth::id() === $task->user_id){
            
            //タスク詳細ビューでそれを表示
            return view('tasks.show', [
                
                'task' => $task,
            ]);
            
        }else{

            return view('auth.login');
            
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    //タスク詳細画面
    public function edit($id)
    {

        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        if(\Auth::id() === $task->user_id){

            //タスク詳細ビューでそれを表示
            return view('tasks.edit', [
                'task' => $task,
            ]);
            
        }else{

            return view('auth.login');
            
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    //タスクの更新画面
    public function update(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            'content' => 'required|max:255',
            'status' => 'required|max:10'
        ]);
        
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        //タスクを更新
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    //タスクの削除
    public function destroy($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) {
            
            $task->delete();
        }
        
        return back();
    }
}
