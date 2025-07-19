# リスコフ置換原則（LSP）と実装パターン整理メモ

## 1. レイヤーをまず意識する

### 設計原則レイヤー

* **LSP (Liskov Substitution Principle)**

  * **サブタイプはスーパータイプの〝代わりに差し替えても挙動が壊れない〟ことが必須**。差し替えた瞬間にバグるなら継承関係そのものが設計ミス。 ([Zenn][1])

  * 契約（Contract）の三大ルール

    1. **前提条件を強めない**（入力チェックを厳しくしない）
    2. **事後条件・戻り値保証を弱めない**
    3. **クラス不変条件を崩さない**（状態遷移ルールを守る）
       加えて *引数型は**逆変換**･戻り値型は**共変換***、新しい例外は親のサブタイプ以内、といったシグネチャ制約もある。 ([Wikipedia][2])

### 実装パターンレイヤー

| パターン                       | キーワード   | 役割・ポイント                             |
| -------------------------- | ------- | ----------------------------------- |
| **Contract Programming**   | *契約テスト* | 前提/事後/不変を明示 → テストで代替可否を自動検証し破綻を早期検知 |
| **Composition / Strategy** | *委譲*    | 振る舞い差分は継承ではなく委譲で表現し、契約破壊を根本から避ける    |
| **final / sealed**         | *封印*    | 代替不可能な基底型は継承禁止で LSP 違反をコンパイル時に封じ込め  |

> **ポイント** : LSP＝「壊れない差し替え」という契約。守れないなら**継承しない・封じる・委譲する**の三択で回避。

---

## 2. ミニマル図で見る違反例

```mermaid
classDiagram
    class Rectangle{
        +setWidth()
        +setHeight()
        +area()
    }
    class Square
    Rectangle <|-- Square
note right of Square
setWidth が高さも変更し
Rectangle テストが崩壊
```

* **Square** を **Rectangle** に代入 → 面積テストが失敗 ⇒ **典型的 LSP 違反**。 ([Wikipedia][2])

---

## 3. 実装パターン比較早見表

| 機能        | Contract Programming | Composition / Strategy | final / sealed     |
| --------- | -------------------- | ---------------------- | ------------------ |
| 主目的       | 契約を**明文化＋自動検証**      | 継承を避け差分を委譲             | 継承経路を**物理遮断**      |
| 使い所       | クリティカル API／SDK       | 異種ロジック多数・切替頻繁          | 基底型の安全を最優先したいライブラリ |
| LSP との距離感 | 契約破りをテストで検出          | 継承しないので破綻リスクゼロ         | 破綻経路そのものをなくす       |

---

## 4. よくある誤解 & リカバリ

| 誤解                       | 問題点                       | リカバリ策                               |
| ------------------------ | ------------------------- | ----------------------------------- |
| **“is‑a 関係なら必ず継承”**      | 振る舞い契約が一致する保証はない          | まず委譲（Strategy）を検討。契約を守れると確信したときだけ継承 |
| サブクラスで**例外を追加**          | 呼び出し側が握り潰す or 分岐地獄 → 契約破壊 | 例外可能性を基底契約へ昇格させる / 継承をやめる           |
| **前提条件を強めがち（null 禁止など）** | 親型を期待したコードが子型だけ特別扱いし始める   | 前提条件は弱く、事後条件は強くが鉄則                  |

---

## 5. 実務チェックリスト

1. **基底のユニットテストにサブクラスを流し込んでも全部パスするか？**
2. **サブクラスで前提条件を厳格化していないか？**
3. **事後条件・不変条件を緩めていないか？**
4. **挙動差分を if／instanceof で吸収していないか？**
5. **継承が本当に必要か？　委譲・インターフェース分離で代替できないか？**

---

## 6. Before / After リファクタリング例

### Before（LSP 違反）

```php
class ReadOnlyFile extends File {
    public function write(string $data) {
        throw new ItsReadOnlyFileException(); // 基底の契約を破る
    }
}
```

### After（委譲＋インターフェースで回避）

```php
interface WritableFile {
    public function write(string $data): void;
}

class File implements WritableFile { /* ... */ }

class ReadOnlyFile {
    public function __construct(private File $inner) {}
    public function read(): string   { return $this->inner->read();  }
    // write() を公開しない＝契約破綻を根本から排除
}
```

---

### まとめ一行

> **LSPは「差し替えても壊れない」という継承契約の番人。契約を守れないと判明したら、継承ではなく委譲か封印で解決せよ。**

[1]: https://zenn.dev/nakurei/books/solid-principle-kanzen-rikai/viewer/liskov-substitution-principle?utm_source=chatgpt.com "Liskov substitution principle（リスコフの置換原則） - Zenn"
[2]: https://en.wikipedia.org/wiki/Liskov_substitution_principle "Liskov substitution principle - Wikipedia"
