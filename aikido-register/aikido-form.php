<div class='aikido-register-form'>
    <?php global $success_message, $errors; ?>
    <?php if (isset($success_message)) : ?>
        <div style='margin-top: 20px' class="alert alert-success"><?= $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors)) : ?>
        <div style='margin-top: 20px' class="alert alert-danger">
            <?php
            foreach ($errors as $key =>  $error) {
                if (is_string($error)) {
                    echo $error . '<br />';
                }
                else {
                    echo $error[0] . '<br />';
                }
            }
            ?>
        </div>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data" name="aikido_register_form">
        <p>To be filled in by Member</p>
        <table class="membership">
            <tbody>
            <tr>
                <td class="div-5">Application No.: ask/oa/1414/160330</td>
                <td align="right"><span class="smalllabel">Please upload 1 recent passport size photograph
<input type="file" name="graphic" id="passport"></span></td>
            </tr>
            <tr>
                <td class="half"><span class="smalllabel">Family Name:</span><br>
                    <input type="text" name="firstname" id="firstname" value="">
                </td>
                <td><span class="smalllabel">Given Name:</span><br>
                    <input type="text" name="lastname" id="lastname" value="">
                </td>
            </tr>
            <tr>
                <td class="half"><span class="smalllabel">Chinese Name:</span><br>
                    <input type="text" name="chinesename" id="chinesename" value="">
                </td>
                <td class="half"><span class="smalllabel">User Login:</span><br>
                    <input type="text" name="user_login" id="user-login" value="">
                </td>
            </tr>
            <tr>
                <td ><span class="smalllabel">Password</span><br>
                    <input type="password" name="pass" id="pass" value="">
                </td>
                <td ><span class="smalllabel">Confirm Password</span><br>
                    <input type="password" name="pass_confirm" id="pass-confirm" value="">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="div-5"><span class="smalllabel">Gender:</span><br>
                        <select name="gender" id="gender">
                            <option value="m">Male</option>
                            <option value="f">Female</option>
                        </select>
                    </div>
                    <div class="div-5"><span class="smalllabel">BC/NRIC/FIN No:</span><br>
                        <input type="text" name="pass1" id="pass1" value="">
                    </div>
                    <div class="div-5"><span class="smalllabel">Race:</span><br>
                        <input type="text" name="race" id="race" value="">
                    </div>
                    <div class="div-5"><span class="smalllabel">Nationality:</span><br>
                        <input type="text" name="nationality" id="nationality" value="">
                    </div>
                    <div class="div-5"><span class="smalllabel">Marital Status:</span><br>
                        <select name="mstatus" id="mstatus">
                            <option value=""></option>
                            <option value="1">Single</option>
                            <option value="2">Married</option>
                            <option value="3">Divorced</option>
                            <option value="4">Widowed</option>
                        </select>
                    </div>
                    <div class="div-5"><span class="smalllabel">Date of Birth:</span><br>
                        <select name="aik_day" id="day" onchange="updateAge()">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                        </select>
                        <select name="aik_month" id="month" onchange="updateAge()">
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="3">March</option>
                            <option value="4">April</option>
                            <option value="5">May</option>
                            <option value="6">June</option>
                            <option value="7">July</option>
                            <option value="8">August</option>
                            <option value="9">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <input type="text" name="aik_year" id="year" value="" style="width:220px;" onchange="updateAge()">
                    </div>
                    <div class="div-5">
                        <span class="smalllabel">Age:</span><br>
                        <span id="agedisplay">30</span>
                        <input type="hidden" name="age" id="age" value="30"></input>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="div-2"><span class="smalllabel">Block:</span><br>
                        <input type="text" name="block" id="block" value=""></div>
                    <div class="div-2"><span class="smalllabel">Unit:</span><br>
                        <input type="text" name="address2" id="address2" value="">

                        <div>
                        </div>
                    </div>
                </td>
                <td><span class="smalllabel">Building:</span><br>
                    <input type="text" name="building" id="building" value=""></td>

            </tr>

            <tr>
                <td colspan="2"><span class="smalllabel">Street:</span><br>
                    <input type="text" name="address1" id="address1" value=""></td>
            </tr>
            <tr>
                <td><span class="smalllabel">Country:</span><br>
                    <select name="country" class="large" onkeypress="return handleEnter(this, event)"
                            onfocus="highlightField(this,1)" onblur="normalField(this)">
                        <option value="Singapore">Singapore</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Hong Kong">Hong Kong</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Brunei">Brunei</option>
                        <option value="China">China</option>
                        <option value="England">England</option>
                        <option value="USA">USA</option>
                        <option value="Others">Others</option>
                    </select></td>
                <td><span class="smalllabel">Postal Code:</span><br>
                    <input type="text" name="postcode" id="postcode" value=""></td>

            </tr>

            <tr>
                <td>
                    <div class="div-2"><span class="smalllabel">Email Address:</span><br>
                        <input type="text" name="user_email" id="email" value="">
                    </div>
                    <div class="div-2"><span class="smalllabel">Contact Number (Mobile):</span><br>
                        <input type="text" name="mobile" id="mobile" value="">
                    </div>
                </td>
                <td>
                    <div class="div-2"><span class="smalllabel">Contact Number (Home):</span><br>
                        <input type="text" name="telephone" id="telephone" value="">
                    </div>
                    <div class="div-2"><span class="smalllabel">Contact Number (Office):</span><br>
                        <input type="text" name="officephone" id="officephone" value="">
                    </div>
                </td>
            </tr>
            <tr>
                <td><span class="smalllabel">Occupation:</span><br>
                    <input type="text" name="occupation" id="occupation" value="">
                </td>
                <td><span class="smalllabel">Company:</span><br>
                    <input type="text" name="company" id="company" value="">
                </td>
            </tr>
            <tr>
                <td><span class="smalllabel">Previous Martial Arts Experience &amp; Grade:</span><br>
                    <input type="text" name="prev_exp" id="prev_exp">
                </td>
                <td>
                    <div class="div-2"><span class="smalllabel">Are you an ex-student of Aikido Shinju-kai?:</span><br>
                        <label><input type="radio" name="ex_student" value="1" class="ex_student"> Yes</label>
                        <label><input type="radio" name="ex_student" value="2" class="ex_student"> No</label>
                    </div>
                    <div class="div-2"><span class="smalllabel">If yes, please specify</span><br>
                        <input type="text" name="student_yes" id="student_yes">
                    </div>
                </td>
            </tr>
            <tr>
                <td><span class="smalllabel">Please list any past injuries/medical problems if any:<br>(e.g. heart attack, high blood
pressure, diabetes, asthma, allergies, fractures etc)</span><br>
                    <input type="text" name="past_injuries" id="past_injuries" value="">
                </td>
                <td>
                    <div class="div-2"><span class="smalllabel">Have you ever been convicted in the court of Law for any criminal
offence?</span><br>
                        <label><input type="radio" name="been_in_court" value="1" class="been_in_court"> Yes</label>
                        <label><input type="radio" name="been_in_court" value="2" class="been_in_court"> No</label>
                    </div>
                    <div class="div-2"><span class="smalllabel">If yes, please specify</span><br>
                        <input type="text" name="court_yes" id="court_yes" value="">
                    </div>
                </td>
            </tr>
            <tr>
                <td><span class="smalllabel">PDPA:Do you want to receive newsletters and emails from us?</span><br>
                </td>
                <td>
                    <div class="div-2"><span class="smalllabel"></span><br>
                        <label><input type="radio" name="pdpa" value="1" class="pdpa"> Yes</label>
                        <label><input type="radio" name="pdpa" value="2" class="pdpa"> No</label>
                    </div>
                    <div class="div-2"><span class="smalllabel">Consent Date:</span><br>
                        <input type="text" name="pdpa_date" id="pdpa_date" value="" onchange="formatDate(this)">
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="div-5"><span class="smalllabel">Membership:</span><br>
                        <label><input type="radio" name="membership_fee" value="1" checked="checked" class="pdpa"> Membership Fee $32.10</label><br/>
                        <label><input type="radio" name="annual_fee" value="1" checked="checked" class="pdpa"> Annual Membership $38.40</label>
                    </div>
                    <div class="div-5">
                        <span class="smalllabel">Training Attire Thin Gi:</span><br>
                        <select name="thin_attire" id="thin-attire">
                            <option value="0">Thin Gi, Size 100 – 130 ($48.20)</option>
                            <option value="1">Thin Gi, Size 140 – 170 ($53.50)</option>
                            <option value="2">Thin Gi Size 180 ($58.90)</option>
                            <option value="3">Thin Gi Size 190 and 200 ($64.20)</option>
                        </select>
                    </div>
                    <div class="div-5">
                        <span class="smalllabel">Training Attire Thick Gi:</span><br>
                        <select name="thick_attire" id="thick-attire">
                            <option value="0">Thick Gi, Size 150 – 170 ($85.60)</option>
                            <option value="1">Thick Gi Size 180 and 190 ($96.30)</option>
                            <option value="2">Thick Gi Size 200 ($101.70)</option>
                        </select>
                    </div>
                    <div class="div-5">
                        <span class="smalllabel">Select Package:</span><br>
                        <select name="membership_package" id="membership-package">
                            <optgroup label="Once a week">
                                <option value="0">1 Session @ $20.30</option>
                                <option value="1">12 Sessions (1 term) @ $235.40</option>
                            </optgroup>
                            <optgroup label="All sessions package">
                                <option value="2">1 Session @ $28.90</option>
                                <option selected value="3">12 Sessions (1 term) @ $342.40</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="div-2 qty">
                        <span class="smalllabel">Qty:</span><br>
                        <select name="qty" id="qty" disabled>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                    <div class="div-2 total">
                        <span class="smalllabel">Total:</span><br>
                        <span id='total_span'></span><br>
                        <input id="total" name='total' type="hidden" value="546.70">
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <?php wp_nonce_field('aikido_register_nonce', 'aikido_register_nonce', false); ?>
        <input value='Register' class='btn btn-primary btn-lg' type='submit' name='aikido_register_submitted'/>
    </form>
</div>