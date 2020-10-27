/**
 * A concept implementation in morphic.js 
 */
var ConceptMorph;

ConceptMorph.prototype = new BoxMorph();
ConceptMorph.prototype.constructor = ConceptMorph;
ConceptMorph.uber = BoxMorph.prototype;

function ConceptMorph(concept) {
    this.init(concept);
}

ConceptMorph.prototype.init = function (concept) {

    this.concept = concept;

    ConceptMorph.uber.init.call(this);

    this.isDraggable = true;
    this.fps = 25;

    this.label = null;
    this.summary = null;
    this.resizer = null;

    this.buildPanes();

    this.setExtent(
        new Point(
            MorphicPreferences.handleSize * 20,
            MorphicPreferences.handleSize * 20 * 2 / 3
        )
    );
};

ConceptMorph.prototype.buildPanes = function () {
    this.children.forEach(m => {
        m.destroy();
    });
    this.children = [];

    this.label = new TextMorph(this.concept.title);
    this.label.fontSize = MorphicPreferences.menuFontSize;
    this.label.isBold = true;
    this.label.color = WHITE;
    /*
    this.label.mouseDownLeft = function (pos) {
        console.log(pos);
        this.escalateEvent('mouseDownLeft', pos);
    };
    */
    this.add(this.label);

    this.summary = new ScrollFrameMorph();
    this.summary.acceptsDrops = false;
    this.summary.contents.acceptsDrops = false;
    this.summary.isTextLineWrapping = true;
    this.summary.color = WHITE;
    this.summary.hBar.alpha = 0.6;
    this.summary.vBar.alpha = 0.6;
    ctrl = new TextMorph(this.concept.summary);
    ctrl.isEditable = true;
    ctrl.enableSelecting();
    this.summary.setContents(ctrl);
    this.add(this.summary);

    this.resizer = new HandleMorph(
        this,
        150,
        100,
        this.edge,
        this.edge
    );

    this.fixLayout();
};

ConceptMorph.prototype.fixLayout = function () {
    var x, y, r, b, w, h;

    // label
    x = this.left() + this.edge;
    y = this.top() + this.edge;
    r = this.right() - this.edge;
    w = r - x;
    this.label.setPosition(new Point(x, y));
    this.label.setWidth(w);
    if (this.label.height() > (this.height() - 50)) {
        this.bounds.setHeight(this.label.height() + 50);
    }

    // summary
    r = this.right() - this.edge;
    w = r - x;
    y = this.label.bottom() + 2;
    b = this.bottom() - this.edge;
    h = b - y;
    this.summary.setPosition(new Point(x, y));
    this.summary.setExtent(new Point(w, h));

    // resizer
    this.resizer.fixLayout();
};