<?php
/**
 * 補助金・助成金情報サイト - 利用規約ページ（完全版）
 * Grant & Subsidy Information Site - Terms of Service Page (Complete)
 * @package Grant_Insight_Terms
 * @version 1.1-complete
 */

// セキュリティチェック
if (!defined('ABSPATH')) {
    exit('Direct access forbidden.');
}

get_header(); // ヘッダーを読み込み

// 構造化データ
$terms_schema = array(
    '@context' => 'https://schema.org',
    '@type' => 'WebPage',
    'name' => '利用規約 - 補助金インサイト',
    'description' => '補助金インサイトの利用規約。サービス利用に関する規定、禁止事項、免責事項などを定めています。',
    'url' => 'https://joseikin-insight.com/terms/',
    'datePublished' => '2025-10-09',
    'dateModified' => '2025-10-09'
);
?>

<!-- SEO タグ削除 (2025-11-26): header.phpで一元管理 -->

<!-- 構造化データ -->
<script type="application/ld+json">
<?php echo wp_json_encode($terms_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
</script>

<article class="gov-terms-page" itemscope itemtype="https://schema.org/WebPage">
    
    <!-- Breadcrumb -->
    <nav class="gov-breadcrumb" aria-label="パンくずリスト">
        <div class="gov-container">
            <ol class="gov-breadcrumb-list">
                <li class="gov-breadcrumb-item"><a href="<?php echo home_url('/'); ?>">ホーム</a></li>
                <li class="gov-breadcrumb-item current" aria-current="page">利用規約</li>
            </ol>
        </div>
    </nav>
    
    <!-- ページヘッダー -->
    <header class="gov-page-header">
        <div class="gov-container">
            <div class="gov-header-content">
                <h1 class="gov-page-title" itemprop="headline">利用規約</h1>
                <div class="gov-page-meta">
                    <p class="gov-meta-item">制定日：<time datetime="2025-10-09">2025年10月9日</time></p>
                    <p class="gov-meta-item">最終改定日：<time datetime="2025-10-09">2025年10月9日</time></p>
                </div>
            </div>
        </div>
    </header>
    
    <!-- メインコンテンツ -->
    <div class="gov-page-content">
        <div class="gov-container">
            
            <!-- 前文 -->
            <section class="gov-content-section gov-preamble-section">
                <div class="gov-preamble">
                    <p>
                        補助金インサイト（以下「当サイト」といいます）の運営者である中澤圭志（以下「運営者」といいます）が提供する「補助金インサイト」（https://joseikin-insight.com/、以下「本サービス」といいます）の利用に関して、利用者（以下「ユーザー」といいます）との間で以下のとおり利用規約（以下「本規約」といいます）を定めます。
                    </p>
                </div>
            </section>
            
            <!-- 目次 -->
            <section class="content-section toc-section">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="8" y1="6" x2="21" y2="6"/>
                            <line x1="8" y1="12" x2="21" y2="12"/>
                            <line x1="8" y1="18" x2="21" y2="18"/>
                            <line x1="3" y1="6" x2="3.01" y2="6"/>
                            <line x1="3" y1="12" x2="3.01" y2="12"/>
                            <line x1="3" y1="18" x2="3.01" y2="18"/>
                        </svg>
                    </span>
                    目次
                </h2>
                <nav class="toc-nav">
                    <ul class="toc-list">
                        <li><a href="#article-1" class="toc-link">第1条（適用）</a></li>
                        <li><a href="#article-2" class="toc-link">第2条（利用登録）</a></li>
                        <li><a href="#article-3" class="toc-link">第3条（ユーザーIDおよびパスワードの管理）</a></li>
                        <li><a href="#article-4" class="toc-link">第4条（サービス内容）</a></li>
                        <li><a href="#article-5" class="toc-link">第5条（利用料金および支払方法）</a></li>
                        <li><a href="#article-6" class="toc-link">第6条（禁止事項）</a></li>
                        <li><a href="#article-7" class="toc-link">第7条（本サービスの提供の停止等）</a></li>
                        <li><a href="#article-8" class="toc-link">第8条（著作権）</a></li>
                        <li><a href="#article-9" class="toc-link">第9条（利用制限および登録抹消）</a></li>
                        <li><a href="#article-10" class="toc-link">第10条（免責事項）</a></li>
                        <li><a href="#article-11" class="toc-link">第11条（サービス内容の変更等）</a></li>
                        <li><a href="#article-12" class="toc-link">第12条（利用規約の変更）</a></li>
                        <li><a href="#article-13" class="toc-link">第13条（個人情報の取扱い）</a></li>
                        <li><a href="#article-14" class="toc-link">第14条（通知または連絡）</a></li>
                        <li><a href="#article-15" class="toc-link">第15条（権利義務の譲渡の禁止）</a></li>
                        <li><a href="#article-16" class="toc-link">第16条（準拠法・裁判管轄）</a></li>
                        <li><a href="#article-17" class="toc-link">第17条（反社会的勢力の排除）</a></li>
                        <li><a href="#article-18" class="toc-link">第18条（分離可能性）</a></li>
                    </ul>
                </nav>
            </section>
            
            <!-- 第1条 -->
            <section class="content-section article-section" id="article-1">
                <h2 class="article-title">第1条（適用）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>本規約は、ユーザーと当社との間の本サービスの利用に関わる一切の関係に適用されるものとします。</li>
                        <li>当社は本サービスに関し、本規約のほか、プライバシーポリシー、ご利用ガイド等の諸規定（以下「個別規定」といいます）を定める場合があります。</li>
                        <li>本規約と個別規定の内容が矛盾する場合には、個別規定の内容が優先されるものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第2条 -->
            <section class="content-section article-section" id="article-2">
                <h2 class="article-title">第2条（利用登録）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>本サービスにおいて、登録希望者が当社の定める方法によって利用登録を申請し、当社がこれを承認することによって、利用登録が完了するものとします。</li>
                        <li>当社は、利用登録の申請者に以下の事由があると判断した場合、利用登録の申請を承認しないことがあり、その理由については一切の開示義務を負わないものとします。
                            <ul class="sub-list">
                                <li>利用登録の申請に際して虚偽の事項を届け出た場合</li>
                                <li>本規約に違反したことがある者からの申請である場合</li>
                                <li>反社会的勢力等（暴力団、暴力団員、右翼団体、反社会的勢力、その他これに準ずる者を意味します）である、または資金提供その他を通じて反社会的勢力等の維持、運営若しくは経営に協力若しくは関与する等反社会的勢力等との何らかの交流若しくは関与を行っていると当社が判断した場合</li>
                                <li>その他、当社が利用登録を相当でないと判断した場合</li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </section>
            
            <!-- 第3条 -->
            <section class="content-section article-section" id="article-3">
                <h2 class="article-title">第3条（ユーザーIDおよびパスワードの管理）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>ユーザーは、自己の責任において、本サービスのユーザーIDおよびパスワードを適切に管理するものとします。</li>
                        <li>ユーザーは、いかなる場合にも、ユーザーIDおよびパスワードを第三者に譲渡または貸与し、もしくは第三者と共用することはできません。</li>
                        <li>当社は、ユーザーIDとパスワードの組み合わせが登録情報と一致してログインされた場合には、そのユーザーIDを登録しているユーザー自身による利用とみなします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第4条 -->
            <section class="content-section article-section" id="article-4">
                <h2 class="article-title">第4条（サービス内容）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>本サービスは、以下の機能を提供します：
                            <ul class="sub-list">
                                <li>補助金・助成金に関する情報の検索・閲覧機能</li>
                                <li>地域別・業種別・目的別の制度検索機能</li>
                                <li>申請に関する基本的な情報提供</li>
                                <li>関連ニュース・制度改正情報の配信</li>
                                <li>その他当社が適宜追加する機能</li>
                            </ul>
                        </li>
                        <li>本サービスで提供される情報は、各省庁・自治体等の公式発表に基づいて収集・整理されておりますが、情報の完全性・正確性・最新性を保証するものではありません。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第5条 -->
            <section class="content-section article-section" id="article-5">
                <h2 class="article-title">第5条（利用料金および支払方法）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>本サービスの基本機能は無料で提供されます。</li>
                        <li>当社は、有料サービスを提供する場合、事前にユーザーに料金体系を明示し、同意を得た上で提供いたします。</li>
                        <li>有料サービスの料金は、当社が別途定める方法により支払うものとします。</li>
                        <li>ユーザーは、本サービスの利用料金の支払を遅滞した場合、遅滞した日から完済の日まで年14.6％の割合による遅延損害金を支払うものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第6条 -->
            <section class="content-section article-section" id="article-6">
                <h2 class="article-title">第6条（禁止事項）</h2>
                <div class="article-content">
                    <p>ユーザーは、本サービスの利用にあたり、以下の行為をしてはなりません。</p>
                    <ol class="article-list">
                        <li>法令または公序良俗に違反する行為</li>
                        <li>犯罪行為に関連する行為</li>
                        <li>反社会的勢力に対して直接または間接に利益を供与する行為</li>
                        <li>当社、他のユーザー、または第三者の知的財産権、肖像権、プライバシー、名誉その他の権利または利益を侵害する行為</li>
                        <li>当社、他のユーザー、または第三者を誹謗中傷し、または名誉を傷つける行為</li>
                        <li>当社、他のユーザー、または第三者に不利益、損害、不快感を与える行為</li>
                        <li>本サービスによって得られた情報を営利目的で利用する行為</li>
                        <li>当社のサービスの運営を妨害するおそれのある行為</li>
                        <li>不正アクセスをし、またはこれを試みる行為</li>
                        <li>本サービスの逆アセンブル、逆コンパイル、リバースエンジニアリング等を行う行為</li>
                        <li>本サービスに関連して、反社会的勢力に対して直接または間接に利益を供与する行為</li>
                        <li>補助金・助成金申請における虚偽申請・不正受給を推奨・支援する行為</li>
                        <li>その他、当社が不適切と判断する行為</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第7条 -->
            <section class="content-section article-section" id="article-7">
                <h2 class="article-title">第7条（本サービスの提供の停止等）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>当社は、以下のいずれかの事由があると判断した場合、ユーザーに事前に通知することなく本サービスの全部または一部の提供を停止または中断することができるものとします。
                            <ul class="sub-list">
                                <li>本サービスにかかるコンピュータシステムの保守点検または更新を行う場合</li>
                                <li>地震、落雷、火災、停電または天災などの不可抗力により、本サービスの提供が困難となった場合</li>
                                <li>コンピュータまたは通信回線等が事故により停止した場合</li>
                                <li>各省庁・自治体等の情報システムに障害が発生し、情報収集が困難となった場合</li>
                                <li>その他、当社が本サービスの提供が困難と判断した場合</li>
                            </ul>
                        </li>
                        <li>当社は、本サービスの提供の停止または中断により、ユーザーまたは第三者が被ったいかなる不利益または損害についても、一切の責任を負わないものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第8条 -->
            <section class="content-section article-section" id="article-8">
                <h2 class="article-title">第8条（著作権）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>ユーザーは、自ら著作権等の必要な知的財産権を有するか、または必要な権利者の許諾を得た文章、画像や映像等の情報に関してのみ、本サービスを利用し、投稿ないしアップロードすることができるものとします。</li>
                        <li>ユーザーが本サービスを利用して投稿ないしアップロードした文章、画像、映像等の著作権については、当該ユーザーその他既存の権利者に留保されるものとします。ただし、当社は、本サービスを利用して投稿ないしアップロードされた文章、画像、映像等について、本サービスの改良、品質の向上、または不備の是正等ならびに本サービスの周知宣伝等に必要な範囲で利用できるものとし、ユーザーは、この利用に関して、著作者人格権を行使しないものとします。</li>
                        <li>前項本文の定めるものを除き、本サービスおよび本サービスに関連する一切の情報についての著作権およびその他の知的財産権はすべて当社または当社にその利用を許諾した権利者に帰属し、ユーザーは無断で複製、譲渡、貸与、翻訳、改変、転載、公衆送信（送信可能化を含みます）、伝送、配布、出版、営業使用等をしてはならないものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第9条 -->
            <section class="content-section article-section" id="article-9">
                <h2 class="article-title">第9条（利用制限および登録抹消）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>当社は、ユーザーが以下のいずれかに該当する場合には、事前の通知なく、投稿データを削除し、ユーザーに対して本サービスの全部もしくは一部の利用を制限しまたはユーザーとしての登録を抹消することができるものとします。
                            <ul class="sub-list">
                                <li>本規約のいずれかの条項に違反した場合</li>
                                <li>登録事項に虚偽の事実があることが判明した場合</li>
                                <li>料金等の支払債務の不履行があった場合</li>
                                <li>当社からの連絡に対し、一定期間返答がない場合</li>
                                <li>本サービスについて、最終の利用から一定期間利用がない場合</li>
                                <li>その他、当社が本サービスの利用を適当でないと判断した場合</li>
                            </ul>
                        </li>
                        <li>当社は、本条に基づき当社が行った行為によりユーザーに生じた損害について、一切の責任を負いません。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第10条 -->
            <section class="content-section article-section highlight-section" id="article-10">
                <h2 class="article-title">第10条（免責事項）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>当社は、本サービスに事実上または法律上の瑕疵（安全性、信頼性、正確性、完全性、有効性、特定の目的への適合性、セキュリティなどに関する欠陥、エラーやバグ、権利侵害などを含みます）がないことを明示的にも黙示的にも保証しておりません。</li>
                        <li>当社は、本サービスで提供される補助金・助成金に関する情報について、以下の点を明示的に保証いたしません：
                            <ul class="sub-list">
                                <li>情報の完全性、正確性、最新性</li>
                                <li>申請条件の詳細な要件</li>
                                <li>採択・承認の可能性</li>
                                <li>制度の継続性・変更可能性</li>
                            </ul>
                        </li>
                        <li>当社は、本サービスに起因してユーザーに生じたあらゆる損害について、当社の故意または重過失による場合を除き、一切の責任を負いません。ただし、本サービスに関する当社とユーザーとの間の契約（本規約を含みます）が消費者契約法に定める消費者契約となる場合、この免責規定は適用されません。</li>
                        <li>前項ただし書に定める場合であって、当社が責任を負う場合、その責任は、当該損害が発生した月にユーザーが当社に支払った利用料金を上限とし、また、予見の有無を問わず、特別損害、間接損害、逸失利益については責任を負いません。</li>
                        <li>当社は、本サービスに関して、ユーザーと他のユーザーまたは第三者との間において生じた取引、連絡または紛争等について一切責任を負いません。</li>
                    </ol>
                    
                    <div class="disclaimer-box">
                        <h3 class="disclaimer-title">補助金・助成金申請に関する特別免責事項</h3>
                        <ul class="disclaimer-list">
                            <li>本サービスの利用により補助金・助成金の申請を行った場合の採択・不採択について、当社は一切の責任を負いません</li>
                            <li>申請手続きの結果、発生した損失・機会損失について当社は責任を負いません</li>
                            <li>制度変更・廃止による影響について当社は責任を負いません</li>
                            <li>ユーザーが行った申請における法令違反・不正受給について当社は責任を負いません</li>
                        </ul>
                    </div>
                </div>
            </section>
            
            <!-- 第11条 -->
            <section class="content-section article-section" id="article-11">
                <h2 class="article-title">第11条（サービス内容の変更等）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>当社は、ユーザーへの事前の告知をもって、本サービスの内容を変更、追加または廃止することがあり、ユーザーはこれに同意するものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第12条 -->
            <section class="content-section article-section" id="article-12">
                <h2 class="article-title">第12条（利用規約の変更）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>当社は以下の場合には、ユーザーの個別の同意を要せず、本規約を変更することができるものとします。
                            <ul class="sub-list">
                                <li>本規約の変更がユーザーの一般の利益に適合するとき</li>
                                <li>本規約の変更が本サービス利用契約の目的に反せず、かつ、変更の必要性、変更後の内容の相当性その他の変更に係る事情に照らして合理的なものであるとき</li>
                            </ul>
                        </li>
                        <li>当社はユーザーに対し、前項による本規約の変更にあたり、事前に、本規約を変更する旨および変更後の本規約の内容ならびにその効力発生時期を通知いたします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第13条 -->
            <section class="content-section article-section" id="article-13">
                <h2 class="article-title">第13条（個人情報の取扱い）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>当社は、本サービスの利用によって取得する個人情報については、当社<a href="https://joseikin-insight.com/privacy/" class="text-link">プライバシーポリシー</a>に従い適切に取り扱うものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第14条 -->
            <section class="content-section article-section" id="article-14">
                <h2 class="article-title">第14条（通知または連絡）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>ユーザーと当社との間の通知または連絡は、当社の定める方法によって行うものとします。当社は、ユーザーから、当社が別途定める方式に従った変更届け出がない限り、現在登録されている連絡先が有効なものとみなして当該連絡先へ通知または連絡を行い、これらは、発信時にユーザーへ到達したものとみなします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第15条 -->
            <section class="content-section article-section" id="article-15">
                <h2 class="article-title">第15条（権利義務の譲渡の禁止）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>ユーザーは、当社の書面による事前の承諾なく、利用契約上の地位または本規約に基づく権利もしくは義務を第三者に譲渡し、または担保に供することはできません。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第16条 -->
            <section class="content-section article-section" id="article-16">
                <h2 class="article-title">第16条（準拠法・裁判管轄）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>本規約の解釈にあたっては、日本法を準拠法とします。</li>
                        <li>本サービスに関して紛争が生じた場合には、当社の本店所在地を管轄する裁判所を専属的合意管轄とします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 第17条 -->
            <section class="content-section article-section" id="article-17">
                <h2 class="article-title">第17条（反社会的勢力の排除）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>ユーザーは、自らが、現在および将来にわたって、以下のいずれにも該当しないことを表明し、保証するものとします。
                            <ul class="sub-list">
                                <li>暴力団、暴力団員、暴力団員でなくなった時から5年を経過しない者、暴力団準構成員、暴力団関係企業、総会屋等、社会運動等標ぼうゴロまたは特殊知能暴力集団等、その他これらに準ずる者（以下総称して「暴力団員等」といいます）</li>
                                <li>暴力団員等に対して資金等を提供し、または便宜を供与する等直接的あるいは積極的に暴力団員等の維持運営に協力し、若しくは関与する者</li>
                                <li>暴力団員等が事業活動を支配し、または暴力団員等と社会的に非難されるべき関係を有する者</li>
                            </ul>
                        </li>
                        <li>ユーザーは、自らまたは第三者をして以下の行為を行わないことを保証するものとします。
                            <ul class="sub-list">
                                <li>暴力的な要求行為</li>
                                <li>法的な責任を超えた不当な要求行為</li>
                                <li>取引に関して、脅迫的な言動をし、または暴力を用いる行為</li>
                                <li>風説を流布し、偽計を用いて当社の信用を毀損し、または当社の業務を妨害する行為</li>
                                <li>その他前各号に準ずる行為</li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </section>
            
            <!-- 第18条 -->
            <section class="content-section article-section" id="article-18">
                <h2 class="article-title">第18条（分離可能性）</h2>
                <div class="article-content">
                    <ol class="article-list">
                        <li>本規約のいずれかの条項またはその一部が無効または執行不能と判断された場合であっても、そのことは他の規定の有効性に影響を与えないものとします。</li>
                    </ol>
                </div>
            </section>
            
            <!-- 附則 -->
            <section class="content-section supplementary-section">
                <h2 class="article-title">附　則</h2>
                <div class="article-content">
                    <p>本規約は、2025年10月9日から施行いたします。</p>
                </div>
            </section>
            
            <!-- 関連ページへのリンク -->
            <section class="content-section" id="related-links">
                <h2 class="section-title">
                    <span class="title-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                            <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                        </svg>
                    </span>
                    関連ページ
                </h2>
                <div class="related-links-grid">
                    <a href="https://joseikin-insight.com/about/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4M12 8h.01"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">当サイトについて</h3>
                            <p class="link-card-description">サービス概要と運営情報</p>
                        </div>
                    </a>
                    
                    <a href="https://joseikin-insight.com/contact/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">お問い合わせ</h3>
                            <p class="link-card-description">サービスに関するご質問はこちら</p>
                        </div>
                    </a>
                    
                    <a href="https://joseikin-insight.com/privacy/" class="related-link-card">
                        <div class="link-card-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <div class="link-card-content">
                            <h3 class="link-card-title">プライバシーポリシー</h3>
                            <p class="link-card-description">個人情報の取り扱いについて</p>
                        </div>
                    </a>
                </div>
            </section>
            
            <!-- ページトップへ戻るボタン -->
            <div class="page-top-btn-wrapper">
                <a href="#" class="page-top-btn" aria-label="ページトップへ戻る">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    <span>ページトップへ</span>
                </a>
            </div>
            
        </div>
    </div>
</article>

<style>
/* ==========================================================================
   Official Government Design - Terms of Service Page CSS
   官公庁デザイン - 利用規約ページ
   ========================================================================== */
:root {
    --gov-navy-900: #0d1b2a;
    --gov-navy-800: #1b263b;
    --gov-navy-700: #2c3e50;
    --gov-navy-600: #34495e;
    --gov-navy-500: #415a77;
    --gov-navy-400: #778da9;
    --gov-navy-300: #a3b1c6;
    --gov-navy-200: #cfd8e3;
    --gov-navy-100: #e8ecf1;
    --gov-navy-50: #f4f6f8;
    --gov-gold: #c9a227;
    --gov-gold-light: #d4b77a;
    --gov-gold-pale: #f0e6c8;
    --gov-green: #2e7d32;
    --gov-red: #c62828;
    --gov-white: #ffffff;
    --gov-black: #1a1a1a;
    --gov-gray-900: #212529;
    --gov-gray-800: #343a40;
    --gov-gray-700: #495057;
    --gov-gray-600: #6c757d;
    --gov-gray-500: #adb5bd;
    --gov-gray-400: #ced4da;
    --gov-gray-300: #dee2e6;
    --gov-gray-200: #e9ecef;
    --gov-gray-100: #f8f9fa;
    --gov-font-serif: "Shippori Mincho", "Yu Mincho", serif;
    --gov-font-sans: 'Noto Sans JP', -apple-system, BlinkMacSystemFont, sans-serif;
    --gov-transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    --gov-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
    --gov-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --gov-radius: 4px;
    --gov-radius-lg: 8px;
}

* { box-sizing: border-box; }

.gov-terms-page {
    font-family: var(--gov-font-sans);
    color: var(--gov-gray-900);
    background: linear-gradient(180deg, var(--gov-navy-50) 0%, var(--gov-white) 100%);
    line-height: 1.7;
    min-height: 100vh;
}

.gov-terms-page::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gov-navy-800) 0%, var(--gov-gold) 50%, var(--gov-navy-800) 100%);
    z-index: 9999;
}

.gov-container {
    max-width: 960px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Breadcrumb */
.gov-breadcrumb {
    background: var(--gov-white);
    border-bottom: 1px solid var(--gov-gray-200);
    padding: 16px 0;
}

.gov-breadcrumb-list {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
    font-size: 13px;
}

.gov-breadcrumb-item {
    display: flex;
    align-items: center;
}

.gov-breadcrumb-item:not(:last-child)::after {
    content: '›';
    margin-left: 8px;
    color: var(--gov-gray-400);
}

.gov-breadcrumb-item a {
    color: var(--gov-navy-600);
    text-decoration: none;
    transition: color var(--gov-transition);
}

.gov-breadcrumb-item a:hover {
    color: var(--gov-navy-900);
    text-decoration: underline;
}

.gov-breadcrumb-item.current {
    color: var(--gov-gray-600);
}

/* Header */
.gov-page-header {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-700) 100%);
    padding: 60px 0 50px;
    position: relative;
}

.gov-page-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gov-gold) 0%, var(--gov-gold-light) 50%, var(--gov-gold) 100%);
}

.gov-header-content {
    text-align: center;
}

.gov-page-title {
    font-family: var(--gov-font-serif);
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--gov-white);
    margin: 0 0 20px;
    letter-spacing: 0.1em;
}

.gov-page-meta {
    display: flex;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
}

.gov-meta-item {
    font-size: 0.875rem;
    color: var(--gov-gold-light);
    margin: 0;
}

.gov-page-content {
    padding: 60px 0 80px;
}

.gov-content-section, .content-section {
    margin-bottom: 48px;
}

/* Preamble */
.gov-preamble, .preamble-content {
    background: var(--gov-navy-50);
    border-left: 4px solid var(--gov-gold);
    padding: 24px 28px;
    border-radius: var(--gov-radius);
}

.gov-preamble p, .preamble-content p {
    margin: 0;
    color: var(--gov-gray-800);
    line-height: 1.9;
    font-size: 1rem;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-family: var(--gov-font-serif);
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--gov-navy-800);
}

.title-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: var(--gov-navy-800);
    border-radius: var(--gov-radius);
    color: var(--gov-gold);
}

/* TOC */
.toc-nav {
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius-lg);
    padding: 24px;
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 8px;
}

.toc-link {
    display: block;
    padding: 10px 16px;
    color: var(--gov-navy-800);
    text-decoration: none;
    border-radius: var(--gov-radius);
    transition: all var(--gov-transition);
    font-size: 0.9375rem;
    border-left: 3px solid transparent;
}

.toc-link:hover {
    background: var(--gov-navy-50);
    border-left-color: var(--gov-gold);
    transform: translateX(4px);
}

/* Article Sections */
.article-section {
    scroll-margin-top: 80px;
}

.article-title {
    font-family: var(--gov-font-serif);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gov-white);
    margin: 0 0 24px;
    padding: 16px 20px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-700) 100%);
    border-radius: var(--gov-radius);
}

.article-content {
    padding: 0 8px;
}

.article-content p {
    color: var(--gov-gray-700);
    line-height: 1.8;
    margin-bottom: 20px;
}

.article-list {
    list-style: none;
    counter-reset: article-counter;
    padding: 0;
    margin: 0;
}

.article-list > li {
    counter-increment: article-counter;
    position: relative;
    padding-left: 32px;
    margin-bottom: 20px;
    line-height: 1.8;
    color: var(--gov-gray-700);
}

.article-list > li::before {
    content: counter(article-counter) ".";
    position: absolute;
    left: 0;
    font-weight: 600;
    color: var(--gov-navy-800);
}

.sub-list {
    list-style: none;
    padding: 0;
    margin: 16px 0 0;
}

.sub-list li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 10px;
    line-height: 1.7;
}

.sub-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0.65em;
    width: 6px;
    height: 6px;
    background: var(--gov-gold);
    border-radius: 50%;
}

/* Highlight Section */
.highlight-section {
    background: var(--gov-navy-50);
    border: 2px solid var(--gov-navy-200);
    border-radius: var(--gov-radius-lg);
    padding: 28px;
}

.disclaimer-box {
    background: var(--gov-gray-100);
    border: 1px solid var(--gov-gray-300);
    border-left: 4px solid var(--gov-red);
    padding: 24px;
    border-radius: var(--gov-radius);
    margin-top: 28px;
}

.disclaimer-title {
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 16px;
}

.disclaimer-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.disclaimer-list li {
    position: relative;
    padding-left: 32px;
    margin-bottom: 12px;
    color: var(--gov-gray-800);
    line-height: 1.6;
    font-weight: 500;
}

.disclaimer-list li::before {
    content: '!';
    position: absolute;
    left: 0;
    top: 0;
    width: 22px;
    height: 22px;
    background: var(--gov-red);
    color: var(--gov-white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8125rem;
    font-weight: 700;
}

.text-link {
    color: var(--gov-navy-700);
    text-decoration: underline;
    font-weight: 500;
    transition: color var(--gov-transition);
}

.text-link:hover {
    color: var(--gov-gold);
}

/* Supplementary */
.supplementary-section .article-content {
    background: var(--gov-gray-100);
    border-left: 4px solid var(--gov-navy-600);
    padding: 20px 24px;
    border-radius: var(--gov-radius);
}

.supplementary-section .article-content p {
    margin: 0;
    color: var(--gov-gray-700);
}

/* Related Links */
.related-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.related-link-card {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 20px;
    background: var(--gov-white);
    border: 1px solid var(--gov-gray-200);
    border-radius: var(--gov-radius);
    text-decoration: none;
    transition: all var(--gov-transition);
}

.related-link-card:hover {
    background: var(--gov-navy-50);
    border-color: var(--gov-navy-300);
    transform: translateX(4px);
}

.link-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: var(--gov-navy-100);
    border-radius: var(--gov-radius);
    color: var(--gov-navy-700);
    flex-shrink: 0;
    transition: all var(--gov-transition);
}

.related-link-card:hover .link-card-icon {
    background: var(--gov-gold-pale);
    color: var(--gov-navy-900);
}

.link-card-content { flex: 1; }

.link-card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gov-navy-900);
    margin: 0 0 4px;
}

.link-card-description {
    font-size: 0.8125rem;
    color: var(--gov-gray-600);
    margin: 0;
}

/* Page Top Button */
.page-top-btn-wrapper {
    text-align: center;
    margin-top: 60px;
}

.page-top-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: linear-gradient(135deg, var(--gov-navy-800) 0%, var(--gov-navy-700) 100%);
    color: var(--gov-white);
    text-decoration: none;
    border-radius: var(--gov-radius);
    font-weight: 600;
    transition: all var(--gov-transition);
    box-shadow: var(--gov-shadow-sm);
}

.page-top-btn:hover {
    background: linear-gradient(135deg, var(--gov-navy-900) 0%, var(--gov-navy-800) 100%);
    transform: translateY(-2px);
    box-shadow: var(--gov-shadow);
}

/* Responsive */
@media (max-width: 768px) {
    .gov-page-header {
        padding: 48px 0 40px;
    }
    
    .gov-page-title {
        font-size: 1.875rem;
    }
    
    .gov-page-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .toc-list {
        grid-template-columns: 1fr;
    }
    
    .article-content {
        padding: 0;
    }
    
    .related-links-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .gov-container {
        padding: 0 16px;
    }
    
    .gov-page-title {
        font-size: 1.5rem;
    }
    
    .section-title {
        font-size: 1.25rem;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .article-title {
        font-size: 1.125rem;
        padding: 14px 16px;
    }
}

html {
    scroll-behavior: smooth;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ページトップボタン
    const pageTopBtn = document.querySelector('.page-top-btn');
    if (pageTopBtn) {
        pageTopBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // 目次リンクのスムーススクロール
    const tocLinks = document.querySelectorAll('.toc-link');
    tocLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php get_footer(); // フッターを読み込み ?>