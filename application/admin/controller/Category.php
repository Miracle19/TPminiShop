<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request as ThinkRequest;
use model\Model;
use think\composer\ThinkExtend;

class Category extends Common{
    protected $cmodel;
    public function __construct()
    {
        parent::__construct();
        $this->cmodel = model('Category');
        // halt($this->cmodel);
    }
    public function index(){
        // $data = Db::name('category')->select();
        // $category = $this->cmodel;
        // $data = $category->get(1);
        // model模型操作的数据库获得的是模型对象  读取的数据也是对象
        // $category = new \app\admin\model\Category();
        // $data = $category->get(['cate_name'=>'家用电器']);
        // 返回的模型对象可以调用toarray方法  获取对应data下的属性即数据库值
        // 闭包写法 这里传入的$query参数 可以拿到  对应db类下的$query方法  
        // $category->get(function($query){
        //         return $query->where('id',4)->whereOr('cate_name','家用电器');
        // });
        // echo $category->getLastSql();
        // 模型对象一旦调用  query类中的方法 就会自定转换为query对象 后续就不能再使用模型对象中的方法了
        // 修改默认model模型类的查询数据返回值为其他的可以使用toarry处理数据
        // $data =  $category->all();
        // $data = $data->toArray();
        // dump($data);
        //     $rs= $category->saveAll([
        //         ['cate_name'=>'宝藏男孩','parent_id'=>'30'],
        //         ['cate_name'=>'男孩宝藏','parent_id'=>'31'],
        //     ]);
        //    echo $category->id;
        //    echo $category->getLastInsID();
        //     dump($rs);
        // $rs= $category->save(['cate_name'=>'女孩宝藏']);
        // echo $category->id;
        // echo $category->getlastInsID();
        // dump($rs);
        // exit;
        // 对象格式写入数据
        // $category->cate_name='家用电气';
        // $category->parent_id=11;
        // $category->class_name='baolibaoqi';
        // 过滤掉不属于当前数据表的字段
        // $category->allowField(true)->save();
        // echo $category->id;
        // 修改数据
        // $category->isUpdate(true)->save(['cate_name'=>'家用电器'],['parent_id'=>11]);
        // echo $category->getLastSql();
        // echo  $category->where(['id'=>25])->delete();
        // // echo $category->id;
        // $query = Db::name('class');
        // dump($query->alias('a')->find()->getLastSql());
        // dump($query->field('cate_name','parent_id')->order('id asc')->where('id','20')->page(2,3)->limit(2)->fetchSql()->select());
        // dump($query->fetchSql());exit;;
        // dump($query);
        // $query->where('id',10)->find();
        // echo $query->getLastSql().'<hr/>';
        // $query->where('id = 5 and cate_name ="林林"')->find();
        // echo $query->getLastSql().'<hr/>';
        // $id = 5;
        // $name = 'tandong';
        // $query->where("id = $id and cate_name ='$name'")->find();
        // echo $query->getLastSql().'<hr/>';
        // $query->where("id = :ids and cate_name =:name")->bind(['name'=>$name,'ids'=>$id])->find();  
        // echo $query->getLastSql().'<hr/>';
        // $query->where(['id'=>1])->select();
        // echo $query->getLastSql().'<hr/>';
        // $query->where(
        //     [
        //         'class_id'=>['gt',1],
        //         'id'=>['lt',2]
        //     ]
        // )->select();
        // echo $query->getLastSql().'<hr/>';
        // $query->where(
        //     [
        //         'class_id'=>['gt',1],
        //         'id'=>['lt',2]
        //     ]
        // )->whereOr(['class_name'=>"php"])->select();
        // echo $query->getLastSql().'<hr/>';
        // $query->where('id','<',2)->where('id',4)->select();
        // echo $query->getLastSql();
        // $query->where(function($q){
        //     $q->where('id','gt','1')->where('id',2);
        // })->whereOr(function($q){
        //     $q->where(['class_name'=>'tandong']);
        // })->select();
        // echo $query->getLastSql();
        // exit;
        // 获取模型对象
        // $category_model = model('Category');
        // halt($this->cmodel);
        // $query = Db::name('Category');
        // $rs = $query->count();
        // $rs=$query->where('id','gt',50)->sum('id');
        // $query->field('a.*','b.class_name')->alias('a')->join('tedi_class b','a.id=b.class_id','left')->select();
        // echo $query->getLastSql();
        // Db::transaction(function(){
        //     Db::name('Category')->insert(['id'=>55,'cate_name'=>'家']);
        //     Db::name('Class')->insert(['id'=>3,'class_name'=>'faa']);
        // });
        // exit;
        // echo $rs;
        // 切库
        // $query= Db::connect('dblist.php34blog')->name('blog');
        // dump($query->find());
        // exit;
        $category_model=$this->cmodel;
        $data = $category_model->getTreeList();
        $this->assign('category',$data);
        return $this->fetch('catelist');
    }
    // 依赖注入   依赖request类   得到request对象
    public function cateAdd(ThinkRequest $request){
        // 查询出已有的商品分类信息
        $category_model = $this->cmodel;
        // return '添加商品分类首页';   
        if( $request->isGet()){
            // $data = $
            // $data = Db::name('category')->select();
            // dump($data);exit;
            // 将查询出来的数据  渲染到模板
            // 渲染之前先对数据格式化
            // $data = get_tree($data);
            // dump($data);exit;
            $data =  $category_model->getTreeList();
            $this->assign('category',$data);
            return $this->fetch();
        }
        // 获取post中的参数
        $category_model->save($request->post());
        // Db::name('category')->insert($request->post());
        $this->success('添加新的商品分类成功','index');
    }
    public function cateDel(ThinkRequest $request){
        // 用控制器调用模型中的方法操作数据库
        $category = $this->cmodel;
        // dump(input('id'));exit;
        // echo $request->get('id');exit;
      
        $category->cateDel($request->param('id/d'));
        $this->success('删除成功','index');
    }
    
    public function cateEdit(ThinkRequest $request){
        $category = $this->cmodel;
        // 大体思路来说 控制器就是负责数据传递到视图  然后调用模型操作数据库
        // 如果是get请求   显示表单 并显示当前的分类信息
        if($request->isGet()){
            // 获取当前点击行的分类信息
            $info = $category->get(input('id'));
            // 获取所有的分类数据
            $data = $category->getTreeList();
            // 模板赋值  批量赋值 同时传两组数据
            // dump($data);exit;
            $this->assign(['info'=>$info,'data'=>$data]);
            // $this->assign('category',$info);
            return $this->fetch();
        }
        // 调用模型的添加方法
        // halt(input());
        // halt(input());
        
        $rs= $category->cateEdit(input());
        // halt($rs);
       
        if(true!==$rs){
            $this->error($category->getError());
        }
        $this->success('修改成功','index');
    }
    // public function upload(ThinkRequest $request){
    //    if($request->isGet()){
    //        return $this->fetch();
    //    }
    // //    file方法指定的值 是表单中的name值
    //    $file=$request->file('file');
    // //    $info = $file->validate(['ext'=>'png'])->move('upload');
    // //    if(!$info){
    // //        echo $file->getError();
    // //    }
    // //    批量上传循环
    // $list= [];
    // foreach($file as $v){
    //     $info = $v->validate(['ext'=>'png'])->move('upload');
    //     if(!$info){
    //         $list [] =$file->getError();
    //     }
    //     // echo  '上传的文件名称是'.$info->getFilename().'<hr/>';
    //     // echo '上传的文件的完整目录是/upload/'.$info->getSavename().'<hr/>';
    // }
    // //    dump($file);
     
    // //    dump($info);
    // //   echo $info->getFilename().'<hr/>';
    // //   echo $info->getPathname();
      
    // }

}