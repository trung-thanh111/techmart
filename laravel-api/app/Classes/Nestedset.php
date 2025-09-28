<?php

namespace App\Classes;

use App\Enums\Common;
use Illuminate\Support\Facades\DB;

class Nestedset
{
    protected $data;
    protected $auth;
    protected $params;
    protected $children = [];
    protected $count = 0;

    public function __construct($params = null)
    {
        $this->params = $params;
        $this->auth = auth(Common::API);
    }

    // Lấy dữ liệu từ DB
    public function get()
    {
        $this->data = DB::table($this->params['table'])
            ->select(['id', 'parent_id', 'lft', 'rgt', 'level']) //->select(['id', 'name', 'slug', 'parent_id', 'lft', 'rgt', 'level'])
            ->whereNull('deleted_at')
            ->where('publish', 2) // Chỉ lấy comment đã publish (không bị ẩn)
            ->orderBy('id', 'asc') // Sắp xếp theo ID thay vì lft
            ->get()->toArray();
    }

    // Tạo mảng con đơn giản
    public function buildChildren()
    {
        $this->children = [];

        foreach ($this->data as $item) {
            $parentId = $item->parent_id ?: 0;
            $this->children[$parentId][] = $item->id;
        }
    }

    // Tính lft, rgt, level đệ quy
    public function calculate($nodeId = 0, $level = 0)
    {
        $lft = ++$this->count;

        // Xử lý các node con
        if (isset($this->children[$nodeId])) {
            foreach ($this->children[$nodeId] as $childId) {
                $this->calculate($childId, $level + 1);
            }
        }

        $rgt = ++$this->count;

        // Lưu kết quả cho node hiện tại (bỏ qua node 0)
        if ($nodeId > 0) {
            $this->updateNode($nodeId, $lft, $rgt, $level);
        }
    }

    // Cập nhật một node
    private function updateNode($id, $lft, $rgt, $level)
    {
        DB::table($this->params['table'])
            ->where('id', $id)
            ->update([
                'lft' => $lft,
                'rgt' => $rgt,
                'level' => $level,
            ]);
    }

    // Thực hiện rebuild cây
    public function rebuild()
    {
        $this->count = 0;
        $this->get();
        $this->buildChildren();
        $this->calculate();

        return true;
    }

    // Rebuild chỉ cho comment cụ thể và các comment con của nó
    public function rebuildForComment($commentId)
    {
        // Lấy comment hiện tại
        $comment = DB::table($this->params['table'])
            ->where('id', $commentId)
            ->whereNull('deleted_at')
            ->first();

        if (!$comment) {
            return false;
        }

        // Nếu comment bị ẩn, chỉ cập nhật lại cấu trúc tree cho các comment con
        if ($comment->publish == 0) {
            $this->updateChildrenStructure($commentId);
        } else {
            // Nếu comment được hiện lại, rebuild toàn bộ
            $this->rebuild();
        }

        return true;
    }

    // Cập nhật cấu trúc cho các comment con
    private function updateChildrenStructure($parentId)
    {
        // Lấy tất cả comment con (kể cả bị ẩn)
        $children = DB::table($this->params['table'])
            ->where('parent_id', $parentId)
            ->whereNull('deleted_at')
            ->get();

        foreach ($children as $child) {
            // Nếu comment con cũng bị ẩn, tiếp tục xử lý các con của nó
            if ($child->publish == 0) {
                $this->updateChildrenStructure($child->id);
            }
        }
    }
}
