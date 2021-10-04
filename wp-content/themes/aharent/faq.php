<?php /* Template name: FAQ */
    get_header();
?>

<div class="container">
    <div class="about-image">
        <img src="<?php echo get_template_directory_uri() ?>/assets/img/faq.png" />
    </div>
    
    <div class="about-content">
        <div class="accordion faq-block" id="faq-accordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        TÔI CÓ THỂ ĐẶT THUÊ SẢN PHẨM NHƯ THẾ NÀO?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faq-accordion">
                    <div class="accordion-body">
                    Để bắt đầu thuê sản phẩm tại Aharent, bạn hãy chọn sản phẩm thuê mà bạn yêu thích và thêm vào giỏ hàng. Sau đó bạn có thể dễ dàng tiến hành thanh toán thông qua internet banking.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="heaadingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                        Cách thức nhận hàng như thế nào? Thời gian nhận hàng là bao lâu?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faq-accordion">
                    <div class="accordion-body">
                        Bạn có thể chọn nhận hàng tại nhà hoặc nhận hàng trực tiếp tại cửa hàng. Hiện tại đối tác Aharent hoạt động ở khu vực tp.HCM. Chính vì thế việc vận chuyển sản phẩm thuê đến với khách hàng dao động trong vòng 24 tiếng, tùy theo mặt hàng được chọn.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                        Tôi có thể thuê trong thời gian ngắn nhất là bao lâu?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faq-accordion">
                    <div class="accordion-body">
                    Bạn có thể thuê sản phẩm ít nhất là 1 ngày, tùy vào sản phẩm bạn thuê. Đối với thuê nội thất, thời gian thuê ngắn nhất là 6 tháng.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                    Trong khi thuê, tôi có thể mua sản phẩm đang thuê được không?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faq-accordion">
                    <div class="accordion-body">
                    Có. Bạn có thể liên hệ với đội ngũ chăm sóc khách hàng của Aharent khi có nhu cầu mua lại nhé.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFive">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                    Tôi có thể đổi lại sản phẩm sau khi đã nhận được không?
                    </button>
                </h2>
                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faq-accordion">
                    <div class="accordion-body">
                    Bạn sẽ được kiểm tra sản phẩm khi nhận hàng trực tiếp tại cửa hàng hoặc tại nhà. Trong quá trình kiểm tra sản phẩm, bạn phát hiện ra sản phẩm có dấu hiệu hư hỏng và không thể sử dụng được. Chúng tôi sẽ hỗ trợ bạn thay thế sản phẩm mới cho bạn trong thời gian ngắn nhất. Sau khi hoàn tất quá trình kiểm tra sản phẩm và giao nhận hàng, Aharent sẽ không chịu trách nhiệm về việc đổi trả sản phẩm.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header" id="headingSix">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="true" aria-controls="collapseSix">
                    Aharent có giao hàng ở các tỉnh khác ngoài tp. HCM không?
                    </button>
                </h2>
                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faq-accordion">
                    <div class="accordion-body">
                    Hiện tại Aharent chỉ hoạt động và phục vụ khách hàng tại khu vực tp.HCM. Hi vọng trong tương lai không xa sẽ được phục vụ khách hàng ở các tỉnh, thành phố khác.
                    </div>
                </div>
            </div>
        </div>
        <?php the_content(); ?>
    </div>
</div>




<?php get_footer(); ?>