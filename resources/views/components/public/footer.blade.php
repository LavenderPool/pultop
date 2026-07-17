<footer id="footer" class="footer solid-bg empty-footer">
    <style>
        .column-list {
            column-count: 1;
            column-gap: 20px;
        }

        @media (min-width: 768px) {
            .column-list {
                column-count: 2;
            }
        }

        @media (min-width: 1024px) {
            .column-list {
                column-count: 3;
            }
        }

        @media (min-width: 1200px) {
            .column-list {
                column-count: 4;
            }
        }
    </style>
    <div class="wf-wrap">
        <div class="wf-container-footer" style="border-bottom: solid 1px;">
            <div class="">
                <section id="custom_html-4" class="">
                    <div class="textwidget custom-html-widget">
                        <div>
                            <img class="alignnone size-full wp-image-2597"
                                src="{{ asset('images/pultop-logo_CDR.png') }}" alt=""
                                width="149" height="56">
                            <ul class="column-list" style="color: #949fb2;">
                                <li><img draggable="false" role="img" class="emoji" alt="💰"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f4b0.svg"> Сумовые и валютные
                                    вклады в банках Узбекистана;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="🏆"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f3c6.svg"> Лучшие вклады в
                                    2025 году;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="💻"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f4bb.svg"> Онлайн вклады и
                                    депозиты с капитализацией процентов;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="🎯"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f3af.svg"> Выгодные
                                    потребительские кредиты;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="🚗"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f697.svg"> Автокредиты в
                                    банках Узбекистана;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="🏠"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f3e0.svg"> Ипотечные кредиты
                                    в 2025 году;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="🏘️"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f3d8.svg"> Ипотечные кредиты
                                    на вторичное жильё;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="📄"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f4c4.svg"> Кредиты без
                                    документов;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="🛡️"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f6e1.svg"> Кредит без залога;
                                </li>
                                <li><img draggable="false" role="img" class="emoji" alt="⚡"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/26a1.svg"> Быстрые Онлайн
                                    микрозаймы;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="💳"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f4b3.svg"> Кредитные карты в
                                    Узбекистане;</li>
                                <li><img draggable="false" role="img" class="emoji" alt="📊"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f4ca.svg"> Рейтинги банков;
                                </li>
                                <li><img draggable="false" role="img" class="emoji" alt="🏦"
                                        src="https://s.w.org/images/core/emoji/17.0.2/svg/1f3e6.svg"> Все банки
                                    Узбекистана.</li>
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>


    <!-- !Bottom-bar -->
    <div id="bottom-bar" class="logo-left" role="contentinfo">
        <div class="wf-wrap">
            <div class="wf-container-bottom">


                <div class="wf-float-left">

                    PULTOP.UZ 2019-2026
                </div>


                <div class="wf-float-right">

                    <div class="mini-nav">
                        <ul id="bottom-menu">
                            <li
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-3452 first has-children depth-0">
                                <a href="{{ route('banks.index') }}" data-level="1"><i
                                        class="fa fa-university"></i><span class="menu-item-text"><span
                                            class="menu-text">Банки</span></span></a>
                                <ul class="footer-sub-nav gradient-hover hover-style-bg level-arrows-on">
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2906 first depth-1">
                                        <a href="{{ route('banks.rating') }}" data-level="2"><span
                                                class="menu-item-text"><span class="menu-text">Рейтинг
                                                    Банков</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2585 depth-1">
                                        <a href="{{ route('exchange-rates.index') }}" data-level="2"><span
                                                class="menu-item-text"><span class="menu-text">Официальный курс
                                                    ЦБ</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-4406 has-children depth-1">
                                        <a href="{{ route('exchange-rates.index') }}" data-level="2"><span
                                                class="menu-item-text"><span class="menu-text">Обменный курс
                                                    банков</span></span></a>
                                        <ul class="footer-sub-nav gradient-hover hover-style-bg level-arrows-on">
                                            <li
                                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-34510 first depth-2">
                                                <a href="{{ route('exchange-rates.show', 'usd') }}" data-level="3"><span
                                                        class="menu-item-text"><span class="menu-text">Доллар
                                                            США</span></span></a></li>
                                            <li
                                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-34509 depth-2">
                                                <a href="{{ route('exchange-rates.show', 'eur') }}" data-level="3"><span
                                                        class="menu-item-text"><span
                                                            class="menu-text">Евро</span></span></a></li>
                                            <li
                                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-34508 depth-2">
                                                <a href="{{ route('exchange-rates.show', 'rub') }}" data-level="3"><span
                                                        class="menu-item-text"><span class="menu-text">Российский
                                                            рубль</span></span></a></li>
                                            <li
                                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-34507 depth-2">
                                                <a href="{{ route('exchange-rates.show', 'kzt') }}" data-level="3"><span
                                                        class="menu-item-text"><span class="menu-text">Казахский
                                                            тенге</span></span></a></li>
                                            <li
                                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-34506 depth-2">
                                                <a href="{{ route('exchange-rates.show', 'gbp') }}" data-level="3"><span
                                                        class="menu-item-text"><span class="menu-text">Фунт
                                                            стерлингов</span></span></a></li>
                                            <li
                                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-34505 depth-2">
                                                <a href="{{ route('exchange-rates.show', 'chf') }}" data-level="3"><span
                                                        class="menu-item-text"><span class="menu-text">Швейцарский
                                                            франк</span></span></a></li>
                                            <li
                                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-34504 depth-2">
                                                <a href="{{ route('exchange-rates.show', 'jpy') }}" data-level="3"><span
                                                        class="menu-item-text"><span
                                                            class="menu-text">Иена</span></span></a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li
                                class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-3009 has-children depth-0">
                                <a href="{{ route('credits.all') }}" data-level="1"><i
                                        class="fa fa-hand-o-right"></i><span class="menu-item-text"><span
                                            class="menu-text">Кредиты</span></span></a>
                                <ul class="footer-sub-nav gradient-hover hover-style-bg level-arrows-on">
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2606 first depth-1">
                                        <a href="{{ route('credits.alias.potrebitelskie-krediti') }}" data-level="2"><span
                                                class="menu-item-text"><span
                                                    class="menu-text">Потребительский</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2727 depth-1">
                                        <a href="{{ route('credits.alias.avtokredity-v-uzbekistane') }}" data-level="2"><span
                                                class="menu-item-text"><span
                                                    class="menu-text">Автокредит</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2726 depth-1">
                                        <a href="{{ route('credits.alias.ipotechnye-kredity-v-uzbekistane') }}"
                                            data-level="2"><span class="menu-item-text"><span
                                                    class="menu-text">Ипотека</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2725 depth-1">
                                        <a href="{{ route('credits.alias.bankovskie-mikrozajmy-v-uzbekistane') }}"
                                            data-level="2"><span class="menu-item-text"><span
                                                    class="menu-text">Банковские микрозаймы</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2724 depth-1">
                                        <a href="{{ route('credits.alias.overdraft-v-uzbekistane') }}" data-level="2"><span
                                                class="menu-item-text"><span
                                                    class="menu-text">Овердрафт</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2723 depth-1">
                                        <a href="{{ route('credits.alias.kredity-nachinayushhim-biznesmenam-v-uzbekistane') }}"
                                            data-level="2"><span class="menu-item-text"><span
                                                    class="menu-text">Предпринимателям</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2722 depth-1">
                                        <a href="{{ route('credits.alias.obrazovatelnye-kredity-v-uzbekistane') }}"
                                            data-level="2"><span class="menu-item-text"><span class="menu-text">На
                                                    образование</span></span></a></li>
                                </ul>
                            </li>
                            <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2220 depth-0">
                                <a href="{{ route('deposits.index') }}" data-level="1"><i
                                        class="fa fa-sort-amount-asc"></i><span class="menu-item-text"><span
                                            class="menu-text">Вклады</span></span></a></li>
                            <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-2219 depth-0">
                                <a href="{{ route('cards.index') }}" data-level="1"><i
                                        class="fa fa-credit-card"></i><span class="menu-item-text"><span
                                            class="menu-text">Карты</span></span></a></li>
                            <li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5800 depth-0">
                                <a href="{{ route('gold.show') }}" data-level="1"><span class="menu-item-text"><span
                                            class="menu-text">Золото</span></span></a></li>
                            <li
                                class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-2007 last has-children depth-0">
                                <a href="#" data-level="1"><i class="fa fa-calculator"></i><span
                                        class="menu-item-text"><span class="menu-text">Калькулятор</span></span></a>
                                <ul class="footer-sub-nav gradient-hover hover-style-bg level-arrows-on">
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1996 first depth-1">
                                        <a href="{{ route('calculators.credit') }}" data-level="2"><span
                                                class="menu-item-text"><span class="menu-text">Кредитный
                                                    калькулятор</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1971 depth-1">
                                        <a href="{{ route('calculators.deposit') }}" data-level="2"><span
                                                class="menu-item-text"><span class="menu-text">Калькулятор
                                                    Вкладов</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1990 depth-1">
                                        <a href="{{ route('calculators.mortgage') }}" data-level="2"><span
                                                class="menu-item-text"><span class="menu-text">Калькулятор
                                                    Ипотеки</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1970 depth-1">
                                        <a href="{{ route('calculators.autoloan') }}" data-level="2"><span
                                                class="menu-item-text"><span class="menu-text">Калькулятор
                                                    Автокредита</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1972 depth-1">
                                        <a href="{{ route('calculators.vat') }}" data-level="2"><span
                                                class="menu-item-text"><span class="menu-text">Калькулятор
                                                    НДС</span></span></a></li>
                                    <li
                                        class="menu-item menu-item-type-post_type menu-item-object-page menu-item-1995 depth-1">
                                        <a href="{{ route('calculators.monthly') }}"
                                            data-level="2"><span class="menu-item-text"><span class="menu-text">Расчет
                                                    ежемесячного платежа по кредиту</span></span></a></li>
                                </ul>
                            </li>
                        </ul>
                        <div class="menu-select"><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 16 16"
                                style="enable-background:new 0 0 16 16;" xml:space="preserve">
                                <path class="st0"
                                    d="M2.5,12c0-0.3,0.2-0.5,0.5-0.5h10c0.3,0,0.5,0.2,0.5,0.5s-0.2,0.5-0.5,0.5H3C2.7,12.5,2.5,12.3,2.5,12z M2.5,8c0-0.3,0.2-0.5,0.5-0.5h10c0.3,0,0.5,0.2,0.5,0.5c0,0.3-0.2,0.5-0.5,0.5H3C2.7,8.5,2.5,8.3,2.5,8z M2.5,4c0-0.3,0.2-0.5,0.5-0.5h10c0.3,0,0.5,0.2,0.5,0.5S13.3,4.5,13,4.5H3C2.7,4.5,2.5,4.3,2.5,4z">
                                </path>
                            </svg><select aria-label="Dropdown menu" class="hasCustomSelect"
                                style="appearance: menulist-button; width: 341px; position: absolute; opacity: 0; height: 45px; font-size: 15px; visibility: visible;">
                                <option selected="selected" value="">———</option>
                                <option value="{{ route('banks.index') }}" data-level="1">Банки</option>
                                <option value="{{ route('banks.rating') }}" data-level="2">— Рейтинг Банков
                                </option>
                                <option value="{{ route('exchange-rates.index') }}" data-level="2">— Официальный
                                    курс ЦБ</option>
                                <option value="{{ route('exchange-rates.index') }}" data-level="2">— Обменный курс
                                    банков</option>
                                <option value="{{ route('exchange-rates.show', 'usd') }}" data-level="3">—— Доллар США
                                </option>
                                <option value="{{ route('exchange-rates.show', 'eur') }}" data-level="3">—— Евро
                                </option>
                                <option value="{{ route('exchange-rates.show', 'rub') }}" data-level="3">—— Российский
                                    рубль</option>
                                <option value="{{ route('exchange-rates.show', 'kzt') }}" data-level="3">—— Казахский
                                    тенге</option>
                                <option value="{{ route('exchange-rates.show', 'gbp') }}" data-level="3">—— Фунт
                                    стерлингов</option>
                                <option value="{{ route('exchange-rates.show', 'chf') }}" data-level="3">—— Швейцарский
                                    франк</option>
                                <option value="{{ route('exchange-rates.show', 'jpy') }}" data-level="3">—— Иена
                                </option>
                                <option value="{{ route('credits.all') }}" data-level="1">Кредиты
                                </option>
                                <option value="{{ route('credits.alias.potrebitelskie-krediti') }}" data-level="2">—
                                    Потребительский</option>
                                <option value="{{ route('credits.alias.avtokredity-v-uzbekistane') }}" data-level="2">— Автокредит
                                </option>
                                <option value="{{ route('credits.alias.ipotechnye-kredity-v-uzbekistane') }}" data-level="2">—
                                    Ипотека</option>
                                <option value="{{ route('credits.alias.bankovskie-mikrozajmy-v-uzbekistane') }}" data-level="2">—
                                    Банковские микрозаймы</option>
                                <option value="{{ route('credits.alias.overdraft-v-uzbekistane') }}" data-level="2">— Овердрафт
                                </option>
                                <option value="{{ route('credits.alias.kredity-nachinayushhim-biznesmenam-v-uzbekistane') }}"
                                    data-level="2">— Предпринимателям</option>
                                <option value="{{ route('credits.alias.obrazovatelnye-kredity-v-uzbekistane') }}" data-level="2">—
                                    На образование</option>
                                <option value="{{ route('deposits.index') }}" data-level="1">Вклады</option>
                                <option value="{{ route('cards.index') }}" data-level="1">Карты
                                </option>
                                <option value="{{ route('gold.show') }}" data-level="1">Золото</option>
                                <option value="#" data-level="1">Калькулятор</option>
                                <option value="{{ route('calculators.credit') }}" data-level="2">— Кредитный
                                    калькулятор</option>
                                <option value="{{ route('calculators.deposit') }}" data-level="2">— Калькулятор Вкладов</option>
                                <option value="{{ route('calculators.mortgage') }}" data-level="2">— Калькулятор
                                    Ипотеки</option>
                                <option value="{{ route('calculators.autoloan') }}" data-level="2">— Калькулятор
                                    Автокредита</option>
                                <option value="{{ route('calculators.vat') }}" data-level="2">— Калькулятор НДС
                                </option>
                                <option value="{{ route('calculators.monthly') }}"
                                    data-level="2">— Расчет ежемесячного платежа по кредиту</option>
                            </select><span class="customSelect" style="display: inline-block;"><span
                                    class="customSelectInner" style="width: 321px; display: inline-block;">Калькулятор
                                    Вкладов</span></span><span class="customSelect1" style="visibility: visible;"><span
                                    class="customSelectInner">Navigation</span></span></div>
                    </div>
                </div>

            </div><!-- .wf-container-bottom -->
        </div><!-- .wf-wrap -->
    </div><!-- #bottom-bar -->
</footer>