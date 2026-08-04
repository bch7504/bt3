<?php
// FE/customer/pages/guide.php
if (!defined('APP_RUNNING')) {
    exit('Truy cập trực tiếp bị nghiêm cấm.');
}
?>

<style>
    .guide-header-card {
        background: linear-gradient(135deg, #0b132b 0%, #1c2541 100%);
        border-radius: 24px;
        border: none;
        overflow: hidden;
        position: relative;
    }
    
    .guide-step-num {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
    }
    
    .faq-accordion .accordion-item {
        border-radius: 16px !important;
        border: 1px solid rgba(0,0,0,0.06) !important;
        margin-bottom: 12px;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01);
    }
    
    .faq-accordion .accordion-button {
        font-weight: 600;
        color: #1c2541;
        padding: 18px 20px;
        background-color: #ffffff;
    }
    
    .faq-accordion .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #0d6efd;
        box-shadow: none;
    }
    
    .faq-accordion .accordion-button::after {
        background-size: 1rem;
    }
</style>

<div class="row g-4 result-container">
    <!-- Header banner của trang hướng dẫn -->
    <div class="col-12">
        <div class="card guide-header-card text-white p-5 shadow">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 tracking-wider">
                        <i class="bi bi-info-circle-fill me-1"></i> Cẩm nang người tham gia giao thông
                    </span>
                    <h2 class="display-6 fw-bold mb-2">HƯỚNG DẪN XỬ LÝ VI PHẠM PHẠT NGUỘI</h2>
                    <p class="text-white-50 lead mb-0 fs-6">
                        Quy trình kiểm tra thông tin, thực hiện nghĩa vụ chấp hành nộp phạt và giải đáp các thắc mắc pháp lý thường gặp.
                    </p>
                </div>
                <div class="col-lg-4 text-end d-none d-lg-block">
                    <i class="bi bi-file-earmark-ruled-fill text-warning" style="font-size: 8rem; opacity: 0.15;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quy trình 3 bước xử lý vi phạm -->
    <div class="col-12 mt-5">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-uppercase text-dark">Quy Trình Xử Lý Trong 3 Bước</h4>
            <p class="text-muted small">Vui lòng thực hiện theo các bước sau để đảm bảo giải quyết đúng quy định pháp luật</p>
        </div>
        
        <div class="row g-4">
            <!-- Bước 1 -->
            <div class="col-md-4">
                <div class="card card-premium h-100 p-4 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="guide-step-num bg-primary bg-opacity-10 text-primary mb-3">1</div>
                        <h5 class="fw-bold text-dark mb-2">Tra cứu trực tuyến</h5>
                        <p class="text-muted small mb-0">
                            Nhập biển kiểm soát phương tiện tại Trang chủ để kiểm tra dữ liệu hình ảnh vi phạm được camera giao thông ghi lại.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Bước 2 -->
            <div class="col-md-4">
                <div class="card card-premium h-100 p-4 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="guide-step-num bg-warning bg-opacity-10 text-warning mb-3">2</div>
                        <h5 class="fw-bold text-dark mb-2">Nhận thông báo & Đối chiếu</h5>
                        <p class="text-muted small mb-0">
                            Kiểm tra số quyết định xử phạt, ngày vi phạm và đơn vị phát hiện vi phạm được hiển thị chi tiết trong bảng kết quả.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Bước 3 -->
            <div class="col-md-4">
                <div class="card card-premium h-100 p-4 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="guide-step-num bg-success bg-opacity-10 text-success mb-3">3</div>
                        <h5 class="fw-bold text-dark mb-2">Chấp hành nộp phạt</h5>
                        <p class="text-muted small mb-0">
                            Thực hiện nộp phạt tại Kho bạc Nhà nước, ngân hàng thương mại được ủy nhiệm hoặc qua Cổng Dịch vụ công Quốc gia để hoàn tất thủ tục.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Giải đáp các câu hỏi thường gặp (FAQs) -->
    <div class="col-12 mt-5">
        <h4 class="fw-bold text-dark mb-3"><i class="bi bi-question-circle text-primary me-2"></i>CÂU HỎI THƯỜNG GẶP</h4>
        <div class="accordion faq-accordion" id="faqAccordion">
            
            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header" id="faqHeadingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseOne" aria-expanded="true" aria-controls="faqCollapseOne">
                        1. Thời hạn nộp phạt nguội là bao lâu kể từ khi phát hiện vi phạm?
                    </button>
                </h2>
                <div id="faqCollapseOne" class="accordion-collapse collapse show" aria-labelledby="faqHeadingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary small">
                        Theo quy định pháp luật hiện hành, trong thời hạn 10 ngày làm việc kể từ ngày phát hiện hành vi vi phạm bằng phương tiện, thiết bị kỹ thuật nghiệp vụ, người có thẩm quyền xử phạt của Cảnh sát giao thông sẽ gửi thông báo vi phạm đến chủ phương tiện. Chủ phương tiện có trách nhiệm đến cơ quan chức năng để phối hợp giải quyết trong thời gian ghi trên thông báo.
                    </div>
                </div>
            </div>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header" id="faqHeadingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseTwo" aria-expanded="false" aria-controls="faqCollapseTwo">
                        2. Nếu không nộp phạt nguội đúng hạn thì có bị phạt thêm không?
                    </button>
                </h2>
                <div id="faqCollapseTwo" class="accordion-collapse collapse" aria-labelledby="faqHeadingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary small">
                        Có. Quá thời hạn chấp hành quyết định xử phạt vi phạm hành chính, cá nhân, tổ chức chưa nộp tiền phạt thì sẽ bị cưỡng chế thi hành quyết định xử phạt và cứ mỗi ngày chậm nộp phạt thì cá nhân, tổ chức vi phạm phải nộp thêm 0,05% trên tổng số tiền phạt chưa nộp. Đồng thời, phương tiện vi phạm có thể bị từ chối đăng kiểm.
                    </div>
                </div>
            </div>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header" id="faqHeadingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseThree" aria-expanded="false" aria-controls="faqCollapseThree">
                        3. Có thể nộp phạt nguội trực tuyến bằng cách nào?
                    </button>
                </h2>
                <div id="faqCollapseThree" class="accordion-collapse collapse" aria-labelledby="faqHeadingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary small">
                        Bạn có thể thực hiện thanh toán trực tuyến qua <strong>Cổng Dịch vụ công Quốc gia</strong> (dichvucong.gov.vn) hoặc ứng dụng ngân hàng liên kết, bằng cách nhập mã quyết định xử phạt được cung cấp bởi Cảnh sát giao thông. Sau khi thanh toán thành công, biên lai điện tử sẽ được ghi nhận tự động vào hệ thống xóa nợ phạt nguội.
                    </div>
                </div>
            </div>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header" id="faqHeadingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapseFour" aria-expanded="false" aria-controls="faqCollapseFour">
                        4. Làm thế nào để biết thông tin lỗi phạt nguội đã được xóa khỏi hệ thống?
                    </button>
                </h2>
                <div id="faqCollapseFour" class="accordion-collapse collapse" aria-labelledby="faqHeadingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-secondary small">
                        Sau khi thực hiện nộp tiền phạt tại cơ quan Kho bạc hoặc thực hiện giao dịch nộp trực tuyến thành công, hệ thống xử lý vi phạm của Cảnh sát giao thông sẽ ghi nhận trạng thái chuyển đổi thành "Đã nộp phạt". Bạn có thể quay lại Cổng tra cứu này để tra cứu lại biển kiểm soát phương tiện để xác nhận lỗi vi phạm đã chuyển trạng thái màu xanh hay chưa.
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
