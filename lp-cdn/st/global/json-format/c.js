window.SINGLE_TAB   = "  ";
window.ImgCollapsed = ST_SERVER + "global/json-format/Collapsed.gif";
window.ImgExpanded  = ST_SERVER + "global/json-format/Expanded.gif";
window.QuoteKeys    = true;
function $id(a)
{
    return document.getElementById(a)
}
function IsArray(a)
{
    return a && typeof a === "object" && typeof a.length === "number" && !(a.propertyIsEnumerable("length"))
}
function Process()
{
    SetTab();
    window.IsCollapsible = $id("CollapsibleView").checked;
    var a                = $id("RawJson").value;
    var b                = "";
    try {
        if (a == "") {
            a = '""'
        }
        var c = jsonlint.parse(a);
        if (c) {
            var d                   = eval("[" + a + "]");
            b                       = ProcessObject(d[0], 0, false, false, false);
            $id("Canvas").innerHTML = "<PRE class='CodeContainer'>" + b + "</PRE>"
        }
    } catch (e) {
        $id("Canvas").innerHTML = "<PRE class='fail'>" + e + "</PRE>"
    }
}
window._dateObj   = new Date();
window._regexpObj = new RegExp();
function ProcessObject(g, c, d, k, l)
{
    var h = "";
    var p = (d) ? "<span class='Comma'>,</span> " : "";
    var m = typeof g;
    var o = "";
    if (IsArray(g)) {
        if (g.length == 0) {
            h += GetRow(c, "<span class='ArrayBrace'>[ ]</span>" + p, l)
        } else {
            o = window.IsCollapsible ? '<span><img src="' + window.ImgExpanded + '" onClick="ExpImgClicked(this)" /></span><span class=\'collapsible\'>' : "";
            h += GetRow(c, "<span class='ArrayBrace'>[</span>" + o, l);
            for (var f = 0; f < g.length; f++) {
                h += ProcessObject(g[f], c + 1, f < (g.length - 1), true, false)
            }
            o = window.IsCollapsible ? "</span>" : "";
            h += GetRow(c, o + "<span class='ArrayBrace'>]</span>" + p)
        }
    } else {
        if (m == "object") {
            if (g == null) {
                h += FormatLiteral("null", "", p, c, k, "Null")
            } else {
                if (g.constructor == window._dateObj.constructor) {
                    h += FormatLiteral("new Date(" + g.getTime() + ") /*" + g.toLocaleString() + "*/", "", p, c, k, "Date")
                } else {
                    if (g.constructor == window._regexpObj.constructor) {
                        h += FormatLiteral("new RegExp(" + g + ")", "", p, c, k, "RegExp")
                    } else {
                        var n = 0;
                        for (var b in g) {
                            n++
                        }
                        if (n == 0) {
                            h += GetRow(c, "<span class='ObjectBrace'>{ }</span>" + p, l)
                        } else {
                            o     = window.IsCollapsible ? '<span><img src="' + window.ImgExpanded + '" onClick="ExpImgClicked(this)" /></span><span class=\'collapsible\'>' : "";
                            h += GetRow(c, "<span class='ObjectBrace'>{</span>" + o, l);
                            var e = 0;
                            for (var b in g) {
                                var a = window.QuoteKeys ? '"' : "";
                                h += GetRow(c + 1, "<span class='PropertyName'>" + a + b + a + "</span>: " + ProcessObject(g[b], c + 1, ++e < n, false, true))
                            }
                            o = window.IsCollapsible ? "</span>" : "";
                            h += GetRow(c, o + "<span class='ObjectBrace'>}</span>" + p)
                        }
                    }
                }
            }
        } else {
            if (m == "number") {
                h += FormatLiteral(g, "", p, c, k, "Number")
            } else {
                if (m == "boolean") {
                    h += FormatLiteral(g, "", p, c, k, "Boolean")
                } else {
                    if (m == "function") {
                        if (g.constructor == window._regexpObj.constructor) {
                            h += FormatLiteral("new RegExp(" + g + ")", "", p, c, k, "RegExp")
                        } else {
                            g = FormatFunction(c, g);
                            h += FormatLiteral(g, "", p, c, k, "Function")
                        }
                    } else {
                        if (m == "undefined") {
                            h += FormatLiteral("undefined", "", p, c, k, "Null")
                        } else {
                            h += FormatLiteral(g.toString().split("\\").join("\\\\").split('"').join('\\"'), '"', p, c, k, "String")
                        }
                    }
                }
            }
        }
    }
    return h
}
function FormatLiteral(f, d, b, a, c, e)
{
    if (typeof f == "string") {
        f = f.split("<").join("&lt;").split(">").join("&gt;")
    }
    var g = "<span class='" + e + "'>" + d + f + d + b + "</span>";
    if (c) {
        g = GetRow(a, g)
    }
    return g
}
function FormatFunction(a, d)
{
    var c = "";
    for (var b = 0; b < a; b++) {
        c += window.TAB
    }
    var f = d.toString().split("\n");
    var e = "";
    for (var b = 0; b < f.length; b++) {
        e += ((b == 0) ? "" : c) + f[b] + "\n"
    }
    return e
}
function GetRow(a, d, e)
{
    var c = "";
    for (var b = 0; b < a && !e; b++) {
        c += window.TAB
    }
    if (d != null && d.length > 0 && d.charAt(d.length - 1) != "\n") {
        d = d + "\n"
    }
    return c + d
}
function CollapsibleViewClicked()
{
    $id("CollapsibleViewDetail").style.visibility = $id("CollapsibleView").checked ? "visible" : "hidden";
    Process()
}
function QuoteKeysClicked()
{
    window.QuoteKeys = $id("QuoteKeys").checked;
    Process()
}
function CollapseAllClicked()
{
    EnsureIsPopulated();
    TraverseChildren($id("Canvas"), function (a)
    {
        if (a.className == "collapsible") {
            MakeContentVisible(a, false)
        }
    }, 0)
}
function ExpandAllClicked()
{
    EnsureIsPopulated();
    TraverseChildren($id("Canvas"), function (a)
    {
        if (a.className == "collapsible") {
            MakeContentVisible(a, true)
        }
    }, 0)
}
function MakeContentVisible(b, c)
{
    var a = b.previousSibling.firstChild;
    if (!!a.tagName && a.tagName.toLowerCase() == "img") {
        b.style.display                  = c ? "inline" : "none";
        b.previousSibling.firstChild.src = c ? window.ImgExpanded : window.ImgCollapsed
    }
}
function TraverseChildren(b, c, d)
{
    for (var a = 0; a < b.childNodes.length; a++) {
        TraverseChildren(b.childNodes[a], c, d + 1)
    }
    c(b, d)
}
function ExpImgClicked(c)
{
    var a = c.parentNode.nextSibling;
    if (!a) {
        return
    }
    var b = "none";
    var d = window.ImgCollapsed;
    if (a.style.display == "none") {
        b = "inline";
        d = window.ImgExpanded
    }
    a.style.display = b;
    c.src           = d
}
function CollapseLevel(a)
{
    EnsureIsPopulated();
    TraverseChildren($id("Canvas"), function (b, c)
    {
        if (b.className == "collapsible") {
            if (c >= a) {
                MakeContentVisible(b, false)
            } else {
                MakeContentVisible(b, true)
            }
        }
    }, 0)
}
function TabSizeChanged()
{
    Process()
}
function SetTab()
{
    var a      = $id("TabSize");
    window.TAB = MultiplyString(parseInt(a.options[a.selectedIndex].value), window.SINGLE_TAB)
}
function EnsureIsPopulated()
{
    if (!$id("Canvas").innerHTML && !!$id("RawJson").value) {
        Process()
    }
}
function MultiplyString(a, c)
{
    var d = [];
    for (var b = 0; b < a; b++) {
        d.push(c)
    }
    return d.join("")
}
function SelectAllClicked()
{
    if (!!document.selection && !!document.selection.empty) {
        document.selection.empty()
    } else {
        if (window.getSelection) {
            var b = window.getSelection();
            if (b.removeAllRanges) {
                window.getSelection().removeAllRanges()
            }
        }
    }
    var a = (!!document.body && !!document.body.createTextRange) ? document.body.createTextRange() : document.createRange();
    if (!!a.selectNode) {
        a.selectNode($id("Canvas"))
    } else {
        if (a.moveToElementText) {
            a.moveToElementText($id("Canvas"))
        }
    }
    if (!!a.select) {
        a.select($id("Canvas"))
    } else {
        window.getSelection().addRange(a)
    }
}
function LinkToJson()
{
    var a                         = $id("RawJson").value;
    a                             = escape(a.split("/n").join(" ").split("/r").join(" "));
    $id("InvisibleLinkUrl").value = a;
    $id("InvisibleLink").submit()
};